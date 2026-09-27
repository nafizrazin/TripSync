from pathlib import Path
ROOT = Path(__file__).resolve().parents[2]
REQUIRED = [
    'compose.yaml', '.env.example', 'infrastructure/nginx/default.conf',
    'apps/web/package.json', 'apps/web/app/page.tsx',
    'apps/api/composer.json', 'apps/api/artisan', 'apps/api/bootstrap/app.php',
]

def test_foundation_files_exist():
    missing = [p for p in REQUIRED if not (ROOT / p).exists()]
    assert not missing, f'Missing foundation files: {missing}'
