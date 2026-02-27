<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmortizationLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'year',
        'base_value',
        'annuity_amount',
        'accumulated_amount',
        'book_value',
        'is_posted',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    protected function casts(): array
    {
        return [
            'is_posted' => 'boolean',
        ];
    }
}
