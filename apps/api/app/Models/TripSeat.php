<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
final class TripSeat extends Model { use HasUlids; protected $fillable=['trip_id','seat_layout_seat_id','booking_id','seat_number','fare','status','hold_expires_at','version']; protected function casts(): array { return ['fare'=>'decimal:2','hold_expires_at'=>'immutable_datetime']; } public function trip(): BelongsTo { return $this->belongsTo(Trip::class); } public function booking(): BelongsTo { return $this->belongsTo(Booking::class); } public function seatLayoutSeat(): BelongsTo { return $this->belongsTo(SeatLayoutSeat::class); } }
