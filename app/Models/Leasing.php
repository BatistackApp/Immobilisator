<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leasing extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'provider_id',
        'contract_number',
        'monthly_rent',
        'purchase_option_price',
        'start_date',
        'end_date',
        'option_exercised',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'option_exercised' => 'boolean',
        ];
    }
}
