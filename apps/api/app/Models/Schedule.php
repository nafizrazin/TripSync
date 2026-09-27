<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model;
final class Schedule extends Model { use HasUlids; protected $fillable=['route_id','bus_id','departure_time','arrival_time','days_of_week','base_fare','effective_from','effective_until','status']; protected function casts(): array { return ['days_of_week'=>'array','base_fare'=>'decimal:2','effective_from'=>'date','effective_until'=>'date']; } }
