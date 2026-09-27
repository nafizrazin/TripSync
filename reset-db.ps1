param([switch]$Force)
Set-StrictMode -Version Latest; $ErrorActionPreference='Stop'; Set-Location $PSScriptRoot
if(-not $Force){ $answer=Read-Host 'This deletes all local TripSync database data. Type RESET to continue'; if($answer -ne 'RESET'){ Write-Host 'Cancelled.'; exit 0 } }
& "$PSScriptRoot/backup.ps1" | Out-Null
docker compose run --rm api php artisan migrate:fresh --seed --force
if($LASTEXITCODE -ne 0){ throw 'Database reset failed.' }
Write-Host 'Database reset and demo data restored.' -ForegroundColor Green
