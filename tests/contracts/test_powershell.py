from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]

def read(name):
    p=ROOT/name
    return p.read_text(errors='ignore') if p.exists() else ''

def test_lifecycle_scripts_and_setup_safety_contract():
    required=['setup.ps1','start.ps1','stop.ps1','restart.ps1','status.ps1','test.ps1','reset-db.ps1','backup.ps1','restore.ps1']
    missing=[x for x in required if not (ROOT/x).exists()]
    assert not missing, f'Missing scripts: {missing}'
    setup=read('setup.ps1')
    common=read('scripts/common.ps1')
    assert 'winget' in setup and 'docker compose' in setup
    assert 'Test-WSLReady' in setup and 'Install-WSL' in setup
    assert 'wsl --status *> $null' not in setup
    assert 'function Test-WSLReady' in common
    assert '$env:ComSpec' in common and 'wsl.exe --status >nul 2>&1' in common
    assert 'function Install-WSL' in common and 'Start-Process' in common and '-Verb RunAs' in common
    assert 'docker info *> $null' not in common
    assert 'docker info >nul 2>&1' in common
    assert 'key:generate --show 2>$null' not in setup
    assert 'RandomNumberGenerator]::Fill' not in common
    assert 'RandomNumberGenerator]::Create()' in common and '.GetBytes($bytes)' in common
    assert 'function Refresh-ProcessPath' in common
    assert 'Refresh-ProcessPath' in setup
    assert "Test-Path '.env'" in setup
    assert 'migrate --seed --force' in setup
    reset=read('reset-db.ps1')
    assert 'migrate:fresh --seed --force' in reset and 'Force' in reset
