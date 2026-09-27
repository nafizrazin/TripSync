<?php
namespace App\Application\Booking;
use App\Exceptions\ApiProblem;
use App\Models\{SeatHold,Trip,TripSeat,User};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
final class CreateSeatHoldAction {
    public function execute(User $user, string $tripId, array $seatIds): SeatHold {
        return DB::transaction(function () use ($user,$tripId,$seatIds): SeatHold {
            $trip=Trip::query()->whereKey($tripId)->lockForUpdate()->firstOrFail();
            if($trip->booking_status!=='open' || $trip->departure_at->isPast()) throw new ApiProblem('TRIP_CLOSED','This trip is no longer open for booking.',409);
            $seats=TripSeat::query()->where('trip_id',$tripId)->whereIn('id',$seatIds)->orderBy('id')->lockForUpdate()->get();
            if($seats->count()!==count(array_unique($seatIds))) throw new ApiProblem('SEAT_NOT_FOUND','One or more selected seats do not belong to this trip.',422);
            $now=now();
            foreach($seats as $seat){
                if($seat->status==='held' && $seat->hold_expires_at && $seat->hold_expires_at->lte($now)){
                    DB::table('seat_hold_items')->where('trip_seat_id',$seat->id)->delete();
                    $seat->fill(['status'=>'available','hold_expires_at'=>null,'version'=>$seat->version+1])->save();
                }
                if($seat->status!=='available') throw new ApiProblem('SEAT_UNAVAILABLE',"Seat {$seat->seat_number} is no longer available.",409,['seat_id'=>$seat->id,'seat_number'=>$seat->seat_number]);
            }
            $expiresAt=$now->copy()->addMinutes(10);
            $hold=SeatHold::query()->create(['token'=>(string)Str::uuid(),'user_id'=>$user->id,'trip_id'=>$tripId,'status'=>'active','expires_at'=>$expiresAt]);
            foreach($seats as $seat){ DB::table('seat_hold_items')->insert(['id'=>(string)Str::ulid(),'seat_hold_id'=>$hold->id,'trip_seat_id'=>$seat->id]); $seat->fill(['status'=>'held','hold_expires_at'=>$expiresAt,'version'=>$seat->version+1])->save(); }
            return $hold->load('seats');
        },3);
    }
}
