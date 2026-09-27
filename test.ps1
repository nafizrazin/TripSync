Set-StrictMode -Version Latest; $ErrorActionPreference='Stop'; Set-Location $PSScriptRoot
Write-Host 'Running TripSync verification suite' -ForegroundColor Cyan
docker compose run --rm api composer test
if($LASTEXITCODE -ne 0){ throw 'Backend tests failed.' }
docker build --target builder -t tripsync-web-test ./apps/web
docker run --rm tripsync-web-test npm test
if($LASTEXITCODE -ne 0){ throw 'Frontend tests failed.' }
docker run --rm tripsync-web-test npm run typecheck
if($LASTEXITCODE -ne 0){ throw 'Frontend typecheck failed.' }
& "$PSScriptRoot/scripts/smoke.ps1" -Retries 10
