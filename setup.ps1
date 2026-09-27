param([switch]$SkipPrerequisites)
Set-StrictMode -Version Latest
$ErrorActionPreference='Stop'
. "$PSScriptRoot/scripts/common.ps1"
$root=$PSScriptRoot; Set-Location $root
Write-Host "TripSync automated environment setup" -ForegroundColor Green

if(-not $SkipPrerequisites){
    Write-Step 'Checking Windows prerequisites'
    if($env:OS -ne 'Windows_NT'){ Write-Warn 'Automatic prerequisite installation is Windows-only. Continuing with existing local tools.' }
    else {
        if(-not (Test-Command 'winget')){ throw 'winget is required for zero-touch prerequisite installation. Install Microsoft App Installer, then rerun setup.ps1.' }
        if(-not (Test-WSLReady)){
            Write-Step 'Installing/enabling WSL 2'
            Install-WSL
            Write-Warn 'WSL 2 was enabled. Restart Windows, then run .\setup.ps1 again; setup will continue safely from the beginning.'
            exit 3010
        }
        Write-Ok 'WSL 2 is ready.'
        if(-not (Test-Command 'git')){ Write-Step 'Installing Git'; Invoke-Checked 'winget' @('install','--id','Git.Git','-e','--accept-source-agreements','--accept-package-agreements'); Refresh-ProcessPath }
        if(-not (Test-Command 'pwsh')){ Write-Step 'Installing PowerShell 7'; Invoke-Checked 'winget' @('install','--id','Microsoft.PowerShell','-e','--accept-source-agreements','--accept-package-agreements'); Refresh-ProcessPath }
        if(-not (Test-Command 'docker')){ Write-Step 'Installing Docker Desktop'; Invoke-Checked 'winget' @('install','--id','Docker.DockerDesktop','-e','--accept-source-agreements','--accept-package-agreements'); Refresh-ProcessPath }
    }
}

if(-not (Test-Command 'docker')){ throw 'Docker CLI is not available. Install/start Docker Desktop and rerun setup.ps1.' }
if(-not (Wait-Docker 5)){
    $dockerDesktop=Join-Path $env:ProgramFiles 'Docker\Docker\Docker Desktop.exe'
    if(Test-Path $dockerDesktop){ Write-Step 'Starting Docker Desktop'; Start-Process $dockerDesktop }
    if(-not (Wait-Docker 180)){ throw 'Docker did not become ready. Confirm virtualization/WSL2 is enabled, start Docker Desktop, then rerun setup.ps1.' }
}
Write-Ok 'Docker engine is ready.'
if (-not (Test-Path '.env')) { Write-Step 'Creating first-run environment file' }
Ensure-Env

Write-Step 'Building application containers'
docker compose build
if($LASTEXITCODE -ne 0){ throw 'Docker build failed.' }

$envRaw=Get-Content '.env' -Raw
if($envRaw -match '(?m)^APP_KEY=$'){
    Write-Step 'Generating Laravel application key'
    $keyOutput = docker compose run --rm --no-deps api php artisan key:generate --show
    if($LASTEXITCODE -ne 0){ throw 'Unable to generate APP_KEY.' }
    $key=($keyOutput | Select-Object -Last 1).Trim()
    if(-not $key.StartsWith('base64:')){ throw 'Unable to generate APP_KEY.' }
    Set-EnvValue 'APP_KEY' $key
}

Write-Step 'Starting data services'
docker compose up -d postgres redis mailpit
if($LASTEXITCODE -ne 0){ throw 'Unable to start database/cache services.' }

Write-Step 'Migrating and seeding PostgreSQL'
docker compose run --rm api php artisan migrate --seed --force
if($LASTEXITCODE -ne 0){
    Write-Warn 'Database migration/seed could not start. PostgreSQL diagnostics follow:'
    docker compose logs --tail 120 postgres
    throw 'Database migration/seed failed.'
}

Write-Step 'Starting TripSync'
docker compose up -d
if($LASTEXITCODE -ne 0){
    Write-Warn 'TripSync services did not become healthy. Web diagnostics follow:'
    docker compose logs --tail 120 web
    throw 'Unable to start TripSync services.'
}

if(-not (Test-Path '.tripsync-version')){ '1.1.1' | Set-Content '.tripsync-version' -NoNewline }
Write-Step 'Running health checks'
& "$root/scripts/smoke.ps1" -Retries 30
if($LASTEXITCODE -ne 0){ throw 'Health checks failed.' }

Write-Host "`nTripSync is ready." -ForegroundColor Green
Write-Host 'Application: http://localhost'
Write-Host 'Mail inbox:  http://localhost:8025'
$demoPasswordLine = Get-Content '.env' | Where-Object { $_ -match '^DEMO_USER_PASSWORD=' } | Select-Object -First 1
$demoPassword = if($demoPasswordLine){ ($demoPasswordLine -split '=',2)[1] } else { '<see .env>' }
Write-Host "Demo customer: customer@tripsync.local / $demoPassword"
Write-Host "Demo admin:    admin@tripsync.local / $demoPassword"
