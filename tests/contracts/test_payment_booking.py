from pathlib import Path
ROOT=Path(__file__).resolve().parents[2]
def read(p):
    p=ROOT/p
    return p.read_text(errors='ignore') if p.exists() else ''

def test_booking_payment_state_machine_contract():
    create=read(Path('apps/api/app/Application/Booking/CreateBookingAction.php'))
    confirm=read(Path('apps/api/app/Application/Payment/CompleteSimulationPaymentAction.php'))
    cancel=read(Path('apps/api/app/Application/Booking/CancelBookingAction.php'))
    assert 'DB::transaction' in create and 'HOLD_EXPIRED' in create
    assert 'lockForUpdate()' in confirm and 'PAYMENT_ALREADY_PROCESSED' in confirm
    assert "'status'=>'confirmed'" in confirm and "'status'=>'sold'" in confirm
    assert 'Ticket::query()->firstOrCreate' in confirm
    assert "'status'=>'cancelled'" in cancel and "'status'=>'available'" in cancel
    gateway=read(Path('apps/api/app/Contracts/PaymentGateway.php'))
    assert 'initiate' in gateway and 'refund' in gateway
