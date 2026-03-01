<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Revaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'revaluation_date',
        'fair_value',
        'previous_vnc',
        'gap_amount',
        'expert_name',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'revaluation_date' => 'date',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
