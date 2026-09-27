<?php
namespace App\Http\Resources;
use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource;
final class TripSeatResource extends JsonResource { public function toArray(Request $request): array { return ['id'=>$this->id,'seat_number'=>$this->seat_number,'fare'=>$this->fare,'status'=>$this->status,'hold_expires_at'=>$this->hold_expires_at?->toIso8601String(),'layout'=>['row'=>$this->seatLayoutSeat?->row_number,'column'=>$this->seatLayoutSeat?->column_number,'deck'=>$this->seatLayoutSeat?->deck,'type'=>$this->seatLayoutSeat?->seat_type,'is_window'=>$this->seatLayoutSeat?->is_window,'is_aisle'=>$this->seatLayoutSeat?->is_aisle]]; } }
