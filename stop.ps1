Set-StrictMode -Version Latest; $ErrorActionPreference='Stop'; Set-Location $PSScriptRoot
docker compose stop
if($LASTEXITCODE -ne 0){ throw 'TripSync failed to stop cleanly.' }
