<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum AssetType: string implements HasLabel
{
    case Tangible = 'tangible';
    case Intangible = 'intangible';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Tangible => 'Immobilisation Corporelle',
            self::Intangible => 'Immobilisation Incorporelle',
            default => null,
        };
    }
}
