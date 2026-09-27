from pathlib import Path
ROOT = Path(__file__).resolve().parents[2]


def read(path: str) -> str:
    return (ROOT / path).read_text(errors='ignore')


def test_demo_password_is_generated_locally_not_hardcoded():
    source='\n'.join([
        read('setup.ps1'),
        read('scripts/common.ps1'),
        read('apps/web/app/login/page.tsx'),
        read('apps/api/database/seeders/NovaReferenceSeeder.php'),
        read('apps/api/database/seeders/NovaCommerceSeeder.php'),
    ])
    assert "Hash::make('" not in source
    assert 'DEMO_USER_PASSWORD' in read('.env.example')
    assert 'tripsync_demo_change_me' in read('.env.example')


def test_seeders_resolve_demo_password_into_instance_scope():
    for path in [
        'apps/api/database/seeders/NovaReferenceSeeder.php',
        'apps/api/database/seeders/NovaCommerceSeeder.php',
    ]:
        body=read(path)
        assert 'private string $demoPassword;' in body
        assert '$this->demoPassword = $this->resolveDemoPassword();' in body
        assert 'Hash::make($this->demoPassword)' in body
        assert "env('DEMO_USER_PASSWORD')" in body


def test_login_form_does_not_prefill_a_password():
    login=read('apps/web/app/login/page.tsx')
    assert "const [password,setPassword]=useState('')" in login
