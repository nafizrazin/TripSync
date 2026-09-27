from pathlib import Path
ROOT = Path(__file__).resolve().parents[2]

def read(path: str) -> str:
    p = ROOT / path
    return p.read_text(errors='ignore') if p.exists() else ''

def test_discovery_metadata_migration_and_models_exist():
    migration = read('apps/api/database/migrations/2026_09_28_000008_add_discovery_metadata.php')
    for token in ['short_code','tagline','brand_color','rating','review_count','punctuality_percent','comfort_class','amenities']:
        assert token in migration
    assert 'amenities' in read('apps/api/app/Models/Bus.php')
    assert 'rating' in read('apps/api/app/Models/Operator.php')

def test_trip_resource_is_backwards_compatible_and_enriched():
    resource = read('apps/api/app/Http/Resources/TripResource.php')
    for token in ["'available_seats'", "'operator'", "'rating'", "'punctuality_percent'", "'comfort_class'", "'amenities'"]:
        assert token in resource

def test_public_discovery_and_fare_calendar_routes_exist():
    routes = read('apps/api/routes/web.php')
    assert "discovery/overview" in routes
    assert "trips/fare-calendar" in routes
    meta = read('apps/api/app/Http/Controllers/Api/V1/MetaController.php')
    assert 'discoveryOverview' in meta
    trip = read('apps/api/app/Http/Controllers/Api/V1/TripController.php')
    assert 'fareCalendar' in trip

def test_search_supports_passenger_capacity_guard():
    request = read('apps/api/app/Http/Requests/Booking/SearchTripsRequest.php')
    controller = read('apps/api/app/Http/Controllers/Api/V1/TripController.php')
    assert "'passengers'" in request
    assert 'available_seats_count' in controller
    assert "data['passengers']" in controller or "$data['passengers']" in controller
