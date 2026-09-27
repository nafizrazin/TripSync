<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
final class Location extends Model { use HasUlids; protected $fillable=['name','slug','district','is_active']; protected function casts(): array { return ['is_active'=>'boolean']; } }
