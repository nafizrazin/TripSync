Set-StrictMode -Version Latest; $ErrorActionPreference='Stop'; Set-Location $PSScriptRoot
Write-Host 'TRIPSYNC STATUS' -ForegroundColor Cyan
docker compose ps
Write-Host ''
try { $api=Invoke-RestMethod 'http://localhost/api/v1/health' -TimeoutSec 5; Write-Host "API: $($api.status)" -ForegroundColor Green } catch { Write-Host 'API: unavailable' -ForegroundColor Red }
try { $web=Invoke-RestMethod 'http://localhost/api/health' -TimeoutSec 5; Write-Host "Web: $($web.status)" -ForegroundColor Green } catch { Write-Host 'Web: unavailable' -ForegroundColor Red }
