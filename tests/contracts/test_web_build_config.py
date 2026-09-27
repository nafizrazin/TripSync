from pathlib import Path
import json

ROOT = Path(__file__).resolve().parents[2]


def test_web_typescript_allows_explicit_ts_imports_used_by_node_tests():
    config = json.loads((ROOT / 'apps/web/tsconfig.json').read_text())
    assert config['compilerOptions'].get('allowImportingTsExtensions') is True
