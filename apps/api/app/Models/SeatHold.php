<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsToMany;
final class SeatHold extends Model { use HasUlids; protected $fillable=['token','user_id','trip_id','status','expires_at']; protected function casts(): array { return ['expires_at'=>'immutable_datetime']; } public function seats(): BelongsToMany { return $this->belongsToMany(TripSeat::class,'seat_hold_items','seat_hold_id','trip_seat_id'); } }
