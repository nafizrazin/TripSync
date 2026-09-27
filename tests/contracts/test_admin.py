from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]
def read(p):
    p=ROOT/p
    return p.read_text(errors='ignore') if p.exists() else ''

def test_admin_endpoints_are_role_protected_and_operational():
    routes=read(Path('apps/api/routes/web.php'))
    assert "prefix('admin')" in routes
    assert "role:operations_admin,finance_admin,super_admin" in routes
    admin=read(Path('apps/api/app/Http/Controllers/Api/V1/AdminController.php'))
    for metric in ['gross_booking_value','confirmed_bookings','upcoming_trips','occupancy_percent']:
        assert metric in admin
    page=read(Path('apps/web/app/admin/page.tsx'))
    assert 'TripSync Control' in page and 'Recent bookings' in page and 'Upcoming trips' in page


def test_business_actions_write_audit_events():
    assert 'audit->record' in read(Path('apps/api/app/Application/Payment/CompleteSimulationPaymentAction.php'))
    assert 'audit->record' in read(Path('apps/api/app/Application/Booking/CancelBookingAction.php'))
