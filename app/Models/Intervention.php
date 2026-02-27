<?php

namespace App\Models;

use App\Enums\InterventionType;
use App\Observers\InterventionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([InterventionObserver::class])]
class Intervention extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'provider_id',
        'type',
        'title',
        'description',
        'cost',
        'intervention_date',
        'is_capitalized',
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
            'intervention_date' => 'datetime',
            'is_capitalized' => 'boolean',
            'type' => InterventionType::class,
        ];
    }
}
