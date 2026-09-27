<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model;
final class BookingPassenger extends Model { use HasUlids; protected $fillable=['booking_id','full_name','phone','email','document_type','document_number','gender','date_of_birth']; protected function casts(): array { return ['date_of_birth'=>'date']; } }
