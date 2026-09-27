<?php
namespace App\Application\Booking;
use App\Models\SeatHold;
use Illuminate\Support\Facades\DB;
final class ReleaseSeatHoldAction {
    public function execute(SeatHold $hold, string $status='released'): void {
        DB::transaction(function () use ($hold,$status): void {
            $locked=SeatHold::query()->whereKey($hold->id)->lockForUpdate()->firstOrFail();
            if($locked->status!=='active') return;
            $seatIds=DB::table('seat_hold_items')->where('seat_hold_id',$locked->id)->pluck('trip_seat_id');
            DB::table('trip_seats')->whereIn('id',$seatIds)->where('status','held')->update(['status'=>'available','hold_expires_at'=>null,'version'=>DB::raw('version + 1'),'updated_at'=>now()]);
            DB::table('seat_hold_items')->where('seat_hold_id',$locked->id)->delete();
            $locked->update(['status'=>$status]);
        });
    }
}
