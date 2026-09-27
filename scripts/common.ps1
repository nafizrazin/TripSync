Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Write-Step([string]$Message) { Write-Host "`n==> $Message" -ForegroundColor Cyan }
function Write-Ok([string]$Message) { Write-Host "[OK] $Message" -ForegroundColor Green }
function Write-Warn([string]$Message) { Write-Host "[WARN] $Message" -ForegroundColor Yellow }
function Get-ProjectRoot { return (Resolve-Path (Join-Path $PSScriptRoot '..')).Path }
function Invoke-Checked([string]$File,[string[]]$Arguments) {
    & $File @Arguments
    if ($LASTEXITCODE -ne 0) { throw "$File exited with code $LASTEXITCODE" }
}
function Test-Command([string]$Name) { return $null -ne (Get-Command $Name -ErrorAction SilentlyContinue) }


function Refresh-ProcessPath {
    $machinePath=[Environment]::GetEnvironmentVariable('Path','Machine')
    $userPath=[Environment]::GetEnvironmentVariable('Path','User')
    $parts=@()
    if($machinePath){ $parts += $machinePath }
    if($userPath){ $parts += $userPath }
    if($parts.Count -gt 0){ $env:Path=($parts -join ';') }
}
function Test-WSLReady {
    if (-not (Test-Command 'wsl.exe')) { return $false }

    # Windows PowerShell 5.1 can promote stderr from native commands to a
    # terminating NativeCommandError when $ErrorActionPreference is Stop.
    # Probe through cmd.exe so wsl.exe's diagnostic stderr never enters the
    # PowerShell error pipeline; only its exit code is observed.
    & $env:ComSpec /d /s /c 'wsl.exe --status >nul 2>&1'
    return $LASTEXITCODE -eq 0
}
function Install-WSL {
    if (-not (Test-Command 'wsl.exe')) {
        throw 'wsl.exe is not available on this Windows installation. Install all pending Windows updates, then rerun setup.ps1.'
    }

    Write-Warn 'Windows will request Administrator approval to enable WSL 2.'
    try {
        $process = Start-Process -FilePath 'wsl.exe' -ArgumentList @('--install','--no-distribution') -Verb RunAs -Wait -PassThru
    } catch {
        throw "Unable to start the elevated WSL installer. Approve the Windows UAC prompt and rerun setup.ps1. $($_.Exception.Message)"
    }

    if ($process.ExitCode -ne 0) {
        throw "WSL installation exited with code $($process.ExitCode). Run Windows Update, restart Windows, then rerun setup.ps1."
    }
}
function Wait-Docker([int]$Seconds = 120) {
    $deadline=(Get-Date).AddSeconds($Seconds)
    while((Get-Date) -lt $deadline){ & $env:ComSpec /d /s /c 'docker info >nul 2>&1'; if($LASTEXITCODE -eq 0){ return $true }; Start-Sleep -Seconds 3 }
    return $false
}
function Ensure-Env {
    $root=Get-ProjectRoot; Push-Location $root
    try {
        if (-not (Test-Path '.env')) {
            Copy-Item '.env.example' '.env'
            $bytes=New-Object byte[] 24
            $rng=[Security.Cryptography.RandomNumberGenerator]::Create()
            try { $rng.GetBytes($bytes) } finally { $rng.Dispose() }
            $dbPassword=[Convert]::ToBase64String($bytes).Replace('/','_').Replace('+','-').TrimEnd('=')

            $demoBytes=New-Object byte[] 18
            $demoRng=[Security.Cryptography.RandomNumberGenerator]::Create()
            try { $demoRng.GetBytes($demoBytes) } finally { $demoRng.Dispose() }
            $demoPassword=[Convert]::ToBase64String($demoBytes).Replace('/','_').Replace('+','-').TrimEnd('=')

            $envText=(Get-Content '.env' -Raw).Replace('tripsync_dev_change_me',$dbPassword).Replace('tripsync_demo_change_me',$demoPassword)
            [System.IO.File]::WriteAllText((Join-Path $root '.env'),$envText,(New-Object System.Text.UTF8Encoding($false)))
            Write-Ok 'Created .env with random local development credentials.'
        } else {
            $envText=Get-Content '.env' -Raw
            if($envText -notmatch '(?m)^DEMO_USER_PASSWORD='){
                $demoBytes=New-Object byte[] 18
                $demoRng=[Security.Cryptography.RandomNumberGenerator]::Create()
                try { $demoRng.GetBytes($demoBytes) } finally { $demoRng.Dispose() }
                $demoPassword=[Convert]::ToBase64String($demoBytes).Replace('/','_').Replace('+','-').TrimEnd('=')
                [System.IO.File]::AppendAllText((Join-Path $root '.env'),"`nDEMO_USER_PASSWORD=$demoPassword`n",(New-Object System.Text.UTF8Encoding($false)))
            }
            Write-Ok 'Existing .env preserved.'
        }
    } finally { Pop-Location }
}
function Set-EnvValue([string]$Name,[string]$Value) {
    $root=Get-ProjectRoot; $path=Join-Path $root '.env'; $lines=Get-Content $path
    $found=$false; $out=@(); foreach($line in $lines){ if($line -match "^$([Regex]::Escape($Name))="){ $out += "$Name=$Value"; $found=$true } else { $out += $line } }
    if(-not $found){ $out += "$Name=$Value" }; $out | Set-Content $path
}
