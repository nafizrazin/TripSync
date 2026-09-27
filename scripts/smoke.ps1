param([int]$Retries=20)
Set-StrictMode -Version Latest
$ErrorActionPreference='Stop'

$targets=@('http://localhost/web-health','http://localhost/api/v1/health')
foreach($target in $targets){
    $ok=$false
    $lastError='No response received.'
    for($i=0;$i -lt $Retries;$i++){
        try {
            $r=Invoke-RestMethod $target -TimeoutSec 3
            if($r.status -eq 'ok'){
                $ok=$true
                break
            }
            $lastError="Unexpected response status '$($r.status)'."
        } catch {
            $lastError=$_.Exception.Message
        }
        Start-Sleep -Seconds 2
    }
    if(-not $ok){
        Write-Error "Health check failed: $target. Last error: $lastError"
        exit 1
    }
}
Write-Host '[OK] Web and API health checks passed.' -ForegroundColor Green
exit 0
