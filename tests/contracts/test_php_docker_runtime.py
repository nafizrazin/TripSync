from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]


def test_php_runtime_image_contains_composer_before_runtime_composer_commands():
    dockerfile = (ROOT / 'apps/api/Dockerfile').read_text(errors='ignore')
    copy_line = 'COPY --from=vendor /usr/bin/composer /usr/local/bin/composer'
    dump_line = 'RUN composer dump-autoload --optimize'
    assert copy_line in dockerfile
    assert dump_line in dockerfile
    assert dockerfile.index(copy_line) < dockerfile.index(dump_line)


def test_php_runtime_composer_is_available_for_test_script():
    dockerfile = (ROOT / 'apps/api/Dockerfile').read_text(errors='ignore')
    test_script = (ROOT / 'test.ps1').read_text(errors='ignore')
    assert '/usr/local/bin/composer' in dockerfile
    assert 'docker compose run --rm api composer test' in test_script
