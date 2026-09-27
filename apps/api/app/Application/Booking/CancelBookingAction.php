<?php
namespace App\Application\Booking;
use App\Contracts\PaymentGateway; use App\Support\AuditLogger; use App\Domain\Booking\{CancellationPolicy,FareCalculator}; use App\Exceptions\ApiProblem; use App\Models\{Booking,Payment,TripSeat}; use Illuminate\Support\Facades\DB;
final class CancelBookingAction {
    public function __construct(private readonly CancellationPolicy $policy,private readonly FareCalculator $money,private readonly PaymentGateway $gateway,private readonly AuditLogger $audit) {}
    public function execute(Booking $booking,string $reason='Customer cancellation'): Booking {
        return DB::transaction(function() use($booking,$reason): Booking {
            $booking=Booking::query()->with('trip')->whereKey($booking->id)->lockForUpdate()->firstOrFail();
            if($booking->status!=='confirmed') throw new ApiProblem('BOOKING_NOT_CANCELLABLE','Only confirmed bookings can be cancelled.',409);
            if($booking->trip->departure_at->lte(now())) throw new ApiProblem('BOOKING_NOT_CANCELLABLE','Departed trips cannot be cancelled.',409);
            $seatIds=DB::table('booking_seats')->where('booking_id',$booking->id)->pluck('trip_seat_id')->all(); $seats=TripSeat::query()->whereIn('id',$seatIds)->orderBy('id')->lockForUpdate()->get();
            $payment=Payment::query()->where('booking_id',$booking->id)->where('status','succeeded')->latest('paid_at')->lockForUpdate()->first();
            $minutes=now()->diffInMinutes($booking->trip->departure_at,false); $refundMinor=0;
            if($payment){ $refundMinor=$this->policy->refundMinor($this->money->decimalToMinor($payment->amount),$minutes); if($refundMinor>0) $this->gateway->refund($payment,$refundMinor,$reason); }
            foreach($seats as $seat) $seat->update(['status'=>'available','booking_id'=>null,'hold_expires_at'=>null,'version'=>$seat->version+1]);
            DB::table('tickets')->where('booking_id',$booking->id)->update(['status'=>'void','updated_at'=>now()]);
            $booking->update(['status'=>'cancelled','cancelled_at'=>now()]);
            $this->audit->record('booking.cancelled',$booking,['status'=>'confirmed'],['status'=>'cancelled','refund_minor'=>$refundMinor],$booking->user_id);
            return $booking->refresh();
        },3);
    }
}
