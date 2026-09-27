from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]

def read(path): return (ROOT/path).read_text(errors='ignore') if (ROOT/path).exists() else ''

def test_session_auth_and_error_contract_present():
    web=read(Path('apps/api/routes/web.php'))
    assert "prefix('api/v1')" in web
    for route in ["auth/csrf","auth/register","auth/login","auth/logout","auth/me"]:
        assert route in web
    assert 'Auth::attempt' in read(Path('apps/api/app/Http/Controllers/Api/V1/AuthController.php'))
    assert 'session()->regenerate()' in read(Path('apps/api/app/Http/Controllers/Api/V1/AuthController.php'))
    assert 'session()->invalidate()' in read(Path('apps/api/app/Http/Controllers/Api/V1/AuthController.php'))
    middleware=read(Path('apps/api/app/Http/Middleware/RequireRole.php'))
    assert 'hasRole' in middleware and '403' in middleware
    request_id=read(Path('apps/api/app/Http/Middleware/RequestId.php'))
    assert 'X-Request-ID' in request_id
