<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leasing extends Model
{
    use HasFactory, SoftDeletes;

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

    public function category(): HasOneThrough
    {
        return $this->hasOneThrough(
            AssetCategory::class,
            Asset::class,
            'id',                // Clé étrangère sur la table 'assets' (asset.id)
            'id',                // Clé étrangère sur la table 'asset_categories' (category.id)
            'asset_id',          // Clé locale sur la table 'leasings'
            'asset_category_id'  // Clé locale sur la table 'assets'
        );
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
