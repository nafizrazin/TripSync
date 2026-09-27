<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
final class TransportRoute extends Model { use HasUlids; protected $table='routes'; protected $fillable=['origin_location_id','destination_location_id','code','estimated_duration_minutes','distance_km','status']; public function origin(): BelongsTo { return $this->belongsTo(Location::class,'origin_location_id'); } public function destination(): BelongsTo { return $this->belongsTo(Location::class,'destination_location_id'); } }
