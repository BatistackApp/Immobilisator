<?php

namespace App\Models;

use App\Enums\AssetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'accounting_code_asset',
        'accounting_code_depreciation',
        'accounting_code_provision',
    ];

    protected function casts(): array
    {
        return [
            'type' => AssetType::class,
        ];
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
