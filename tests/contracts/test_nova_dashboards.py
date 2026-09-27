from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]
def read(p):
    path=ROOT/p
    return path.read_text(errors='ignore') if path.exists() else ''

def test_admin_overview_exposes_nova_analytics_without_removing_old_metrics():
    admin=read('apps/api/app/Http/Controllers/Api/V1/AdminController.php')
    for token in ['gross_booking_value','occupancy_percent','revenue_trend','top_routes','operator_performance','booking_statuses','system_health']:
        assert token in admin

def test_admin_ui_has_control_center_visualizations():
    page=read('apps/web/app/admin/page.tsx')
    for token in ['TrendBars','Network intelligence','Top routes','Operator performance','System health']:
        assert token in page

def test_customer_booking_views_are_travel_wallet_oriented():
    listing=read('apps/web/app/bookings/page.tsx')
    detail=read('apps/web/app/bookings/[id]/page.tsx')
    for token in ['travel-wallet','Upcoming journeys','Past journeys','departure-countdown']:
        assert token in listing
    for token in ['Journey command','Boarding readiness','travel-timeline','status-pill']:
        assert token in detail
