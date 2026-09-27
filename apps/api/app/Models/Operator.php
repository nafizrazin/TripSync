<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

final class Operator extends Model {
    use HasUlids;

    protected $fillable = [
        'name','slug','short_code','phone','email','tagline','brand_color',
        'rating','review_count','punctuality_percent','status',
    ];

    protected function casts(): array {
        return [
            'rating' => 'decimal:2',
            'review_count' => 'integer',
            'punctuality_percent' => 'decimal:2',
        ];
    }
}
