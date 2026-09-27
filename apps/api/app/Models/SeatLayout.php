<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\HasMany;
final class SeatLayout extends Model { use HasUlids; protected $fillable=['name','seat_count','columns','status']; public function seats(): HasMany { return $this->hasMany(SeatLayoutSeat::class); } }
