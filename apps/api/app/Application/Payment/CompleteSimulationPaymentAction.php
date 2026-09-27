<?php
namespace App\Application\Payment;
use App\Exceptions\ApiProblem; use App\Support\AuditLogger; use App\Models\{Booking,Payment,SeatHold,Ticket,TripSeat}; use Illuminate\Support\Facades\DB; use Illuminate\Support\Str;
final class CompleteSimulationPaymentAction {
    public function __construct(private readonly AuditLogger $audit) {}
    public function execute(Payment $payment): Payment {
        return DB::transaction(function() use($payment): Payment {
            $payment=Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();
            if($payment->status==='succeeded') return $payment;
            if(!in_array($payment->status,['pending','processing'],true)) throw new ApiProblem('PAYMENT_ALREADY_PROCESSED','This payment can no longer be completed.',409);
            $booking=Booking::query()->whereKey($payment->booking_id)->lockForUpdate()->firstOrFail();
            if($booking->status==='confirmed') return $payment;
            if($booking->status!=='pending_payment') throw new ApiProblem('BOOKING_NOT_PAYABLE','This booking cannot be paid.',409);
            $hold=SeatHold::query()->whereKey($booking->seat_hold_id)->lockForUpdate()->first();
            if(!$hold || $hold->status!=='active' || $hold->expires_at->lte(now())) throw new ApiProblem('HOLD_EXPIRED','The seat hold expired before payment confirmation.',409);
            $seatIds=DB::table('booking_seats')->where('booking_id',$booking->id)->pluck('trip_seat_id')->all();
            $seats=TripSeat::query()->whereIn('id',$seatIds)->orderBy('id')->lockForUpdate()->get();
            if($seats->count()!==count($seatIds) || $seats->contains(fn($s)=>$s->status!=='held' || !$s->hold_expires_at || $s->hold_expires_at->lt(now()))) throw new ApiProblem('HOLD_EXPIRED','The selected seats are no longer held for this booking.',409);
            $payment->update(['status'=>'succeeded','provider_transaction_id'=>$payment->provider_transaction_id ?: 'SIM-'.Str::upper(Str::random(16)),'paid_at'=>now()]);
            DB::table('payment_events')->insert(['id'=>(string)Str::ulid(),'payment_id'=>$payment->id,'event_type'=>'payment.succeeded','provider_event_id'=>'SIM-EVT-'.Str::upper(Str::random(14)),'payload'=>json_encode(['source'=>'simulation']),'occurred_at'=>now(),'created_at'=>now(),'updated_at'=>now()]);
            $booking->update(['status'=>'confirmed','confirmed_at'=>now(),'expires_at'=>null]);
            foreach($seats as $seat){ $seat->update(['status'=>'sold','booking_id'=>$booking->id,'hold_expires_at'=>null,'version'=>$seat->version+1]); }
            $assignments=DB::table('booking_seats')->where('booking_id',$booking->id)->get();
            foreach($assignments as $assignment){ Ticket::query()->firstOrCreate(['booking_id'=>$booking->id,'passenger_id'=>$assignment->passenger_id],['public_id'=>'TKT-'.Str::upper(Str::random(14)),'ticket_number'=>'TS-TKT-'.Str::upper(Str::random(8)),'trip_seat_id'=>$assignment->trip_seat_id,'status'=>'issued','qr_payload'=>'TRIPSYNC:'.hash_hmac('sha256',$booking->booking_reference.':'.$assignment->passenger_id,(string)config('app.key')),'issued_at'=>now()]); }
            DB::table('seat_hold_items')->where('seat_hold_id',$hold->id)->delete(); $hold->update(['status'=>'consumed']);
            $this->audit->record('booking.confirmed',$booking,null,['status'=>'confirmed','payment_id'=>$payment->id],$booking->user_id);
            return $payment->refresh();
        },3);
    }
}
