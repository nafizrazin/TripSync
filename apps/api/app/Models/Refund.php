<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model;
final class Refund extends Model { use HasUlids; protected $fillable=['payment_id','booking_id','amount','currency','status','provider_refund_id','reason','processed_at']; protected function casts(): array { return ['amount'=>'decimal:2','processed_at'=>'immutable_datetime']; } }
