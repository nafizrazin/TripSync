<?php
namespace App\Http\Controllers\Api\V1;
use App\Application\Booking\{CreateSeatHoldAction,ReleaseSeatHoldAction};
use App\Exceptions\ApiProblem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateSeatHoldRequest;
use App\Models\SeatHold;
use Illuminate\Http\{JsonResponse,Request};
final class SeatHoldController extends Controller {
    public function store(CreateSeatHoldRequest $request, CreateSeatHoldAction $action): JsonResponse { $hold=$action->execute($request->user(),$request->string('trip_id')->toString(),$request->input('seat_ids')); return response()->json(['data'=>['id'=>$hold->id,'token'=>$hold->token,'trip_id'=>$hold->trip_id,'expires_at'=>$hold->expires_at->toIso8601String(),'seats'=>$hold->seats->map(fn($s)=>['id'=>$s->id,'seat_number'=>$s->seat_number,'fare'=>$s->fare])]],201); }
    public function show(Request $request, SeatHold $seatHold): JsonResponse { if($seatHold->user_id!==$request->user()->id) throw new ApiProblem('FORBIDDEN','This hold does not belong to you.',403); $seatHold->load('seats'); return response()->json(['data'=>['id'=>$seatHold->id,'token'=>$seatHold->token,'trip_id'=>$seatHold->trip_id,'expires_at'=>$seatHold->expires_at->toIso8601String(),'seats'=>$seatHold->seats->map(fn($s)=>['id'=>$s->id,'seat_number'=>$s->seat_number,'fare'=>$s->fare])]]); }
    public function destroy(Request $request, SeatHold $seatHold, ReleaseSeatHoldAction $action): JsonResponse { if($seatHold->user_id!==$request->user()->id) throw new ApiProblem('FORBIDDEN','This hold does not belong to you.',403); $action->execute($seatHold); return response()->json(['message'=>'Seat hold released.']); }
}
