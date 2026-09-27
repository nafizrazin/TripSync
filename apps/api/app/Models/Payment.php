<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
final class Payment extends Model { use HasUlids; protected $fillable=['public_id','booking_id','provider','provider_transaction_id','idempotency_key','amount','currency','status','initiated_at','authorized_at','paid_at','failed_at','metadata']; protected function casts(): array { return ['amount'=>'decimal:2','initiated_at'=>'immutable_datetime','authorized_at'=>'immutable_datetime','paid_at'=>'immutable_datetime','failed_at'=>'immutable_datetime','metadata'=>'array']; } public function booking(): BelongsTo { return $this->belongsTo(Booking::class); } }
