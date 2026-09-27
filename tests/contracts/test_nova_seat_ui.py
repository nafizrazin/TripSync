from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]
def read(p):
    path=ROOT/p
    return path.read_text(errors='ignore') if path.exists() else ''

def test_trip_page_uses_passenger_count_and_best_seat_recommendation():
    page=read('apps/web/app/trips/[id]/page.tsx')
    for token in ['useSearchParams','passengers','recommendAdjacentSeats','Suggest best','window','aisle']:
        assert token in page

def test_seat_map_has_nova_cabin_and_metadata_states():
    seatmap=read('apps/web/components/SeatMap.tsx')
    for token in ['nova-cabin','seat-position','is_window','is_aisle','layout.type']:
        assert token in seatmap

def test_seat_ui_styles_exist():
    css=read('apps/web/app/globals.css')
    for token in ['nova-cabin','seat-position','seat-recommendation','seat-detail-grid']:
        assert token in css
