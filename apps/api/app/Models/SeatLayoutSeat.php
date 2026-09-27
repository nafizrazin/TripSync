<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model;
final class SeatLayoutSeat extends Model { use HasUlids; public $timestamps=false; protected $fillable=['seat_layout_id','seat_number','row_number','column_number','deck','seat_type','is_window','is_aisle','is_active']; protected function casts(): array { return ['is_window'=>'boolean','is_aisle'=>'boolean','is_active'=>'boolean']; } }
