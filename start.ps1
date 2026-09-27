Set-StrictMode -Version Latest; $ErrorActionPreference='Stop'; . "$PSScriptRoot/scripts/common.ps1"; Set-Location $PSScriptRoot
if(-not (Test-Path '.env')){ throw 'Environment is not initialized. Run .\setup.ps1 first.' }
docker compose up -d
if($LASTEXITCODE -ne 0){ throw 'TripSync failed to start.' }
& "$PSScriptRoot/scripts/smoke.ps1" -Retries 20
