from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]


def test_public_docs_ci_and_version_exist():
    required=[
        'README.md','docs/DEMO_GUIDE.md','docs/architecture.md','docs/legacy-audit.md',
        'docs/api.md','.github/workflows/ci.yml','.tripsync-version'
    ]
    missing=[p for p in required if not (ROOT/p).exists()]
    assert not missing, f'Missing public project files: {missing}'

    readme=(ROOT/'README.md').read_text(errors='ignore')
    for token in ['.\\setup.ps1','DemoPay','docs/DEMO_GUIDE.md','Next.js','Laravel','PostgreSQL']:
        assert token in readme
    assert 'TripSync-Patch-' not in readme
    assert 'patches/' not in readme

    audit=(ROOT/'docs/legacy-audit.md').read_text(errors='ignore')
    for token in ['plaintext','booking_det','action.php','CSRF','transaction']:
        assert token.lower() in audit.lower()
