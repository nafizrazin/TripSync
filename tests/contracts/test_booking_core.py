from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]
def read(p):
    path=ROOT/p
    return path.read_text(errors='ignore') if path.exists() else ''

def test_search_and_hold_use_server_authority_and_locks():
    search=read(Path('apps/api/app/Http/Controllers/Api/V1/TripController.php'))
    assert "whereBetween('departure_at'" in search
    assert "where('booking_status','open')" in search
    hold=read(Path('apps/api/app/Application/Booking/CreateSeatHoldAction.php'))
    assert 'DB::transaction' in hold
    assert 'lockForUpdate()' in hold
    assert "orderBy('id')" in hold
    assert 'SEAT_UNAVAILABLE' in hold
    assert 'hold_expires_at' in hold
    routes=read(Path('apps/api/routes/web.php'))
    assert "trips/search" in routes and "seat-holds" in routes
