from pathlib import Path
import json

ROOT = Path(__file__).resolve().parents[2]


def test_next_tracks_security_patches_within_16_3_line():
    package = json.loads((ROOT / 'apps/web/package.json').read_text())
    assert package['dependencies']['next'] == '~16.3.6'


def test_backend_has_executable_phpunit_unit_suite():
    api = ROOT / 'apps/api'
    assert (api / 'phpunit.xml').exists()
    assert (api / 'tests/Unit/FareCalculatorTest.php').exists()
    assert (api / 'tests/Unit/CancellationPolicyTest.php').exists()
