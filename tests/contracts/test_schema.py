from pathlib import Path
ROOT = Path(__file__).resolve().parents[2]
MIG = ROOT / 'apps/api/database/migrations'
MODELS = ROOT / 'apps/api/app/Models'

def test_domain_schema_contract():
    files = list(MIG.glob('*.php')) if MIG.exists() else []
    body = '\n'.join(p.read_text(errors='ignore') for p in files)
    required_tables = [
        'users','roles','role_user','locations','operators','seat_layouts','seat_layout_seats',
        'buses','routes','route_stops','schedules','trips','bookings','trip_seats','seat_holds',
        'seat_hold_items','booking_passengers','booking_seats','payments','payment_events','refunds',
        'tickets','audit_logs','sessions','jobs','failed_jobs'
    ]
    missing = [t for t in required_tables if f"create('{t}'" not in body]
    assert not missing, f'Missing tables: {missing}'
    assert "unique(['trip_id', 'seat_layout_seat_id'])" in body
    assert "decimal('total_amount', 12, 2)" in body


def test_core_models_exist():
    required = ['User.php','Trip.php','TripSeat.php','SeatHold.php','Booking.php','Payment.php','Ticket.php']
    missing = [p for p in required if not (MODELS / p).exists()]
    assert not missing, f'Missing core models: {missing}'
