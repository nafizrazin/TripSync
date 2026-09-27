from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]


def test_overlapping_protected_callback_schedule_has_explicit_name():
    console = (ROOT / 'apps/api/routes/console.php').read_text(errors='ignore')
    name_token = "->name('seat-holds.release-expired')"
    overlap_token = '->withoutOverlapping()'

    assert name_token in console
    assert overlap_token in console
    assert console.index(name_token) < console.index(overlap_token)
