<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'provider_id',
        'principal_amount',
        'interest_rate',
        'duration_months',
        'first_installment_date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function category(): HasOneThrough
    {
        return $this->hasOneThrough(
            AssetCategory::class,
            Asset::class,
            'id',
            'id',
            'asset_id',
            'asset_category_id'
        );
    }

    protected function casts(): array
    {
        return [
            'first_installment_date' => 'date',
        ];
    }
}
