param([Parameter(Mandatory=$true)][string]$BackupPath,[switch]$Force)
Set-StrictMode -Version Latest; $ErrorActionPreference='Stop'; Set-Location $PSScriptRoot
$resolved=(Resolve-Path $BackupPath).Path; $dump=Join-Path $resolved 'database.dump'; if(-not (Test-Path $dump)){ throw "database.dump not found in $resolved" }
if(-not $Force){ $answer=Read-Host 'Restore replaces current local data. Type RESTORE to continue'; if($answer -ne 'RESTORE'){ Write-Host 'Cancelled.'; exit 0 } }
docker compose stop api worker scheduler
try {
    docker compose cp $dump postgres:/tmp/tripsync-restore.dump | Out-Null
    docker compose exec -T postgres sh -lc 'pg_restore -U "$POSTGRES_USER" -d "$POSTGRES_DB" --clean --if-exists --no-owner /tmp/tripsync-restore.dump'
    if($LASTEXITCODE -ne 0){ throw 'Database restore failed.' }
} finally { docker compose up -d api worker scheduler | Out-Null }
& "$PSScriptRoot/scripts/smoke.ps1" -Retries 20
Write-Host 'Restore completed.' -ForegroundColor Green
