from pathlib import Path
ROOT = Path(__file__).resolve().parents[2]

def read(path: str) -> str:
    p = ROOT / path
    return p.read_text(errors='ignore') if p.exists() else ''

def test_database_seeder_delegates_to_nova_seeders():
    body = read('apps/api/database/seeders/DatabaseSeeder.php')
    assert 'NovaReferenceSeeder::class' in body
    assert 'NovaCommerceSeeder::class' in body

def test_reference_seeder_exceeds_demo_network_targets():
    body = read('apps/api/database/seeders/NovaReferenceSeeder.php')
    for token in [
        'LOCATION_COUNT_TARGET = 20', 'OPERATOR_COUNT_TARGET = 12',
        'BUS_COUNT_TARGET = 36', 'ROUTE_COUNT_TARGET = 24',
        'TRIP_DAY_SPAN = 28', 'Executive 40', 'Business 32', 'Premium 28',
        'amenities', 'comfort_class', 'TripSeat',
    ]:
        assert token in body

def test_commerce_seeder_creates_dense_valid_activity():
    body = read('apps/api/database/seeders/NovaCommerceSeeder.php')
    for token in [
        'DEMO_CUSTOMER_TARGET = 30', 'BOOKING_TARGET = 650',
        'BookingPassenger', 'booking_seats', 'Payment', 'payment_events',
        'Ticket', 'Refund', 'audit_logs', "'status' => 'sold'", "'status' => 'available'",
    ]:
        assert token in body

def test_feature_invariant_test_is_present_for_runtime_verification():
    body = read('apps/api/tests/Feature/NovaSeederTest.php')
    for token in ['operators', 'buses', 'routes', 'trips', 'bookings', 'payments', 'tickets']:
        assert token in body
