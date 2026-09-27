from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]
def read(p):
    path=ROOT/p
    return path.read_text(errors='ignore') if path.exists() else ''

def test_nova_home_has_futuristic_discovery_sections():
    home=read('apps/web/app/page.tsx')
    cards=read('apps/web/components/DiscoveryCards.tsx')
    for token in ['NetworkPulse','DiscoveryCards','discovery/overview']:
        assert token in home
    for token in ['popular_routes','top_operators','recent-searches']:
        assert token in cards

def test_nova_design_tokens_and_reduced_motion_exist():
    css=read('apps/web/app/globals.css')
    for token in ['--nova-cyan','--nova-violet','--nova-emerald','nova-grid','aurora','prefers-reduced-motion']:
        assert token in css

def test_search_form_carries_passenger_count():
    form=read('apps/web/components/SearchForm.tsx')
    assert 'passengers' in form
    assert 'UsersRound' in form
