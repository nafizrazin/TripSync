from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]


def read(path: str) -> str:
    return (ROOT / path).read_text(errors='ignore')


def test_postgres18_mount_uses_new_official_volume_root():
    compose = read('compose.yaml')
    assert 'image: postgres:18-alpine' in compose
    assert 'tripsync_pgdata:/var/lib/postgresql\n' in compose
    assert 'tripsync_pgdata:/var/lib/postgresql/data' not in compose


def test_setup_surfaces_postgres_logs_when_migration_cannot_start():
    setup = read('setup.ps1')
    assert "docker compose logs --tail 120 postgres" in setup
