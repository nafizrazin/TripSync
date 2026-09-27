Set-StrictMode -Version Latest; $ErrorActionPreference='Stop'; Set-Location $PSScriptRoot
$stamp=Get-Date -Format 'yyyyMMdd-HHmmss'; $dir=Join-Path $PSScriptRoot "backups/$stamp"; New-Item -ItemType Directory -Force $dir | Out-Null
Write-Host "Creating backup $stamp" -ForegroundColor Cyan
docker compose exec -T postgres sh -lc 'pg_dump -U "$POSTGRES_USER" -d "$POSTGRES_DB" -Fc -f /tmp/tripsync.dump'
if($LASTEXITCODE -ne 0){ throw 'Database backup failed.' }
docker compose cp postgres:/tmp/tripsync.dump "$dir/database.dump" | Out-Null
Copy-Item '.tripsync-version' "$dir/version.txt" -ErrorAction SilentlyContinue
@{ created_at=(Get-Date).ToString('o'); version=((Get-Content '.tripsync-version' -ErrorAction SilentlyContinue) -join '') } | ConvertTo-Json | Set-Content "$dir/metadata.json"
Write-Host "Backup complete: $dir" -ForegroundColor Green
Write-Output $dir
