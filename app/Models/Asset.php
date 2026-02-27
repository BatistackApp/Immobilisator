<?php

namespace App\Models;

use App\Enums\AmortizationMethod;
use App\Enums\AssetStatus;
use App\Enums\FundingType;
use App\Observers\AssetObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([AssetObserver::class])]
class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_category_id',
        'location_id',
        'reference',
        'designation',
        'funding_type',
        'acquisition_value',
        'salvage_value',
        'acquisition_date',
        'service_date',
        'useful_life',
        'gross_value_opening',
        'accumulated_depreciation_opening',
        'amortization_method',
        'status',
        'metadata',
        'depreciable_basis',
    ];

    public function assetCategory(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    protected function casts(): array
    {
        return [
            'status' => AssetStatus::class,
            'funding_type' => FundingType::class,
            'amortization_method' => AmortizationMethod::class,
            'acquisition_date' => 'date',
            'service_date' => 'date',
            'metadata' => 'json',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function localisation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function leasing(): HasOne
    {
        return $this->hasOne(Leasing::class);
    }

    public function loan(): HasOne
    {
        return $this->hasOne(Loan::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }

    public function amortizationLines(): HasMany
    {
        return $this->hasMany(AmortizationLine::class)->orderBy('year');
    }
}
