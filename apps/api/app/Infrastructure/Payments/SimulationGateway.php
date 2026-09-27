<?php
namespace App\Infrastructure\Payments;
use App\Contracts\PaymentGateway; use App\Domain\Booking\FareCalculator; use App\Exceptions\ApiProblem; use App\Models\{Booking,Payment,Refund}; use Illuminate\Support\Str;
final class SimulationGateway implements PaymentGateway {
    public function __construct(private readonly FareCalculator $money) {}
    public function initiate(Booking $booking,string $idempotencyKey): Payment {
        $existing=Payment::query()->where('idempotency_key',$idempotencyKey)->first();
        if($existing){ if($existing->booking_id!==$booking->id) throw new ApiProblem('IDEMPOTENCY_KEY_CONFLICT','This idempotency key belongs to another payment.',409); return $existing; }
        return Payment::query()->create(['idempotency_key'=>$idempotencyKey,'public_id'=>'PAY-'.Str::upper(Str::random(14)),'booking_id'=>$booking->id,'provider'=>'simulation','amount'=>$booking->total_amount,'currency'=>$booking->currency,'status'=>'pending','initiated_at'=>now(),'metadata'=>['mode'=>'simulation']]);
    }
    public function refund(Payment $payment,int $amountMinor,string $reason): Refund { return Refund::query()->create(['payment_id'=>$payment->id,'booking_id'=>$payment->booking_id,'amount'=>$this->money->minorToDecimal($amountMinor),'currency'=>$payment->currency,'status'=>'succeeded','provider_refund_id'=>'SIM-RFD-'.Str::upper(Str::random(12)),'reason'=>$reason,'processed_at'=>now()]); }
}
