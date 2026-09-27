<?php
namespace App\Application\Booking;
use App\Domain\Booking\FareCalculator; use App\Exceptions\ApiProblem; use App\Models\{Booking,BookingPassenger,SeatHold,TripSeat,User}; use Illuminate\Support\Facades\DB; use Illuminate\Support\Str;
final class CreateBookingAction {
    public function __construct(private readonly FareCalculator $money) {}
    public function execute(User $user,string $holdId,array $passengers): Booking {
        return DB::transaction(function() use($user,$holdId,$passengers): Booking {
            $hold=SeatHold::query()->whereKey($holdId)->lockForUpdate()->firstOrFail();
            if($hold->user_id!==$user->id) throw new ApiProblem('FORBIDDEN','This seat hold does not belong to you.',403);
            $existing=Booking::query()->where('seat_hold_id',$hold->id)->where('user_id',$user->id)->first();
            if($existing) return $existing->load(['trip.route.origin','trip.route.destination','passengers']);
            if($hold->status!=='active' || $hold->expires_at->lte(now())) throw new ApiProblem('HOLD_EXPIRED','Your seat hold has expired. Please select seats again.',409);
            $seatIds=DB::table('seat_hold_items')->where('seat_hold_id',$hold->id)->pluck('trip_seat_id')->all();
            $seats=TripSeat::query()->whereIn('id',$seatIds)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            if($seats->count()===0 || $seats->count()!==count($passengers)) throw new ApiProblem('PASSENGER_SEAT_MISMATCH','Provide exactly one passenger for every held seat.',422);
            $requested=collect($passengers)->pluck('trip_seat_id');
            if($requested->unique()->count()!==$requested->count() || $requested->diff($seatIds)->isNotEmpty()) throw new ApiProblem('PASSENGER_SEAT_MISMATCH','Passenger seat assignments must match the held seats.',422);
            foreach($seats as $seat) if($seat->status!=='held' || !$seat->hold_expires_at || $seat->hold_expires_at->lte(now())) throw new ApiProblem('HOLD_EXPIRED','Your seat hold has expired. Please select seats again.',409);
            $subtotalMinor=$seats->sum(fn($seat)=>$this->money->decimalToMinor($seat->fare)); $feeMinor=$this->money->serviceFeeMinor($subtotalMinor); $totalMinor=$this->money->totalMinor($subtotalMinor,$feeMinor);
            $booking=Booking::query()->create(['public_id'=>'BKG-'.Str::upper(Str::random(14)),'booking_reference'=>'TS-'.now()->format('y').'-'.Str::upper(Str::random(7)),'user_id'=>$user->id,'trip_id'=>$hold->trip_id,'seat_hold_id'=>$hold->id,'status'=>'pending_payment','subtotal'=>$this->money->minorToDecimal($subtotalMinor),'discount_amount'=>'0.00','service_fee'=>$this->money->minorToDecimal($feeMinor),'total_amount'=>$this->money->minorToDecimal($totalMinor),'currency'=>'BDT','expires_at'=>$hold->expires_at]);
            foreach($passengers as $data){ $seat=$seats[$data['trip_seat_id']]; $passenger=BookingPassenger::query()->create(['booking_id'=>$booking->id,'full_name'=>$data['full_name'],'phone'=>$data['phone'],'email'=>$data['email']??null]); DB::table('booking_seats')->insert(['id'=>(string)Str::ulid(),'booking_id'=>$booking->id,'passenger_id'=>$passenger->id,'trip_seat_id'=>$seat->id,'fare'=>$seat->fare]); }
            return $booking->load(['trip.route.origin','trip.route.destination','passengers']);
        },3);
    }
}
