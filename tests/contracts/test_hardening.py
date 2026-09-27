from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]
def read(p):
    p=ROOT/p
    return p.read_text(errors='ignore') if p.exists() else ''

def test_container_bootstrap_does_not_run_artisan_before_source_exists():
    docker=read(Path('apps/api/Dockerfile'))
    assert 'composer install --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts' in docker
    assert 'composer dump-autoload --optimize' in docker

def test_health_check_reaches_both_web_and_api():
    nginx=read(Path('infrastructure/nginx/default.conf'))
    smoke=read(Path('scripts/smoke.ps1'))
    assert 'location = /web-health' in nginx
    assert 'http://localhost/web-health' in smoke
    assert 'http://localhost/api/v1/health' in smoke

def test_booking_and_payment_idempotency_guards():
    booking=read(Path('apps/api/app/Application/Booking/CreateBookingAction.php'))
    gateway=read(Path('apps/api/app/Infrastructure/Payments/SimulationGateway.php'))
    assert "where('seat_hold_id',$hold->id)" in booking and 'return $existing' in booking
    assert 'IDEMPOTENCY_KEY_CONFLICT' in gateway

def test_customer_ticket_print_action_is_wired():
    page=read(Path('apps/web/app/bookings/[id]/page.tsx'))
    assert 'window.print()' in page
