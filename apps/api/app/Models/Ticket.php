<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model;
final class Ticket extends Model { use HasUlids; protected $fillable=['public_id','ticket_number','booking_id','passenger_id','trip_seat_id','status','qr_payload','issued_at']; protected function casts(): array { return ['issued_at'=>'immutable_datetime']; } }
