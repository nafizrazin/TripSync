<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Bus extends Model {
    use HasUlids;

    protected $fillable = [
        'operator_id','seat_layout_id','registration_number','label','model',
        'bus_type','comfort_class','amenities','status',
    ];

    protected function casts(): array {
        return ['amenities' => 'array'];
    }

    public function operator(): BelongsTo { return $this->belongsTo(Operator::class); }
    public function seatLayout(): BelongsTo { return $this->belongsTo(SeatLayout::class); }
}
