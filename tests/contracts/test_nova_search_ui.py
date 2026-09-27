from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]
def read(p):
    path=ROOT/p
    return path.read_text(errors='ignore') if path.exists() else ''

def test_search_page_uses_fare_calendar_filters_and_tripscore():
    page=read('apps/web/app/search/page.tsx')
    for token in ['FareCalendar','SearchFilters','filterTrips','sortTrips','labelsForTrip','fare-calendar','passengers']:
        assert token in page

def test_trip_card_exposes_comparison_signals():
    card=read('apps/web/components/TripCard.tsx')
    for token in ['rating','punctuality_percent','amenities','comfort_class','TripScore','labels']:
        assert token in card

def test_search_css_has_filter_and_fare_calendar_layouts():
    css=read('apps/web/app/globals.css')
    for token in ['fare-calendar','search-results-layout','search-filter-panel','trip-score']:
        assert token in css
