from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]


def read(path: str) -> str:
    return (ROOT / path).read_text(errors='ignore')


def test_standalone_web_server_binds_to_all_interfaces_on_port_3000():
    dockerfile = read('apps/web/Dockerfile')
    assert 'ENV PORT=3000' in dockerfile
    assert 'ENV HOSTNAME="0.0.0.0"' in dockerfile
    assert dockerfile.index('ENV PORT=3000') < dockerfile.index('CMD ["node", "server.js"]')
    assert dockerfile.index('ENV HOSTNAME="0.0.0.0"') < dockerfile.index('CMD ["node", "server.js"]')


def test_web_healthcheck_targets_standalone_server_loopback_endpoint():
    compose = read('compose.yaml')
    assert 'http://127.0.0.1:3000/api/health' in compose
    assert 'retries: 12' in compose


def test_setup_surfaces_web_logs_when_full_stack_start_fails():
    setup = read('setup.ps1')
    assert 'docker compose logs --tail 120 web' in setup
