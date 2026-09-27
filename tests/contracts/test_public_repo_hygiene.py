from pathlib import Path
ROOT = Path(__file__).resolve().parents[2]


def test_release_only_artifacts_are_not_in_public_tree():
    assert not (ROOT / 'patches').exists()
    assert not (ROOT / 'docs/superpowers').exists()
    assert not (ROOT / '.pytest_cache').exists()
    assert not list(ROOT.glob('TripSync-Patch-*.ps1'))


def test_legacy_public_snapshot_excludes_raw_sample_dump():
    assert not (ROOT / 'legacy/transport.sql').exists()
    assert (ROOT / 'legacy/schema.sql').exists()
    schema=(ROOT/'legacy/schema.sql').read_text(errors='ignore')
    assert 'INSERT INTO' not in schema
