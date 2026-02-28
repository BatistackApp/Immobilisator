<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum AssetStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Active = 'active';
    case Maintenance = 'maintenance';
    case Disposed = 'disposed';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'primary',
            self::Active => 'success',
            self::Maintenance => 'warning',
            self::Disposed => 'danger',
            default => null,
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Draft => Heroicon::OutlinedPencilSquare,
            self::Active => Heroicon::OutlinedCheckCircle,
            self::Maintenance => Heroicon::OutlinedCog8Tooth,
            self::Disposed => Heroicon::OutlinedTrash,
            default => null,
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Active => 'Actif',
            self::Maintenance => 'En maintenance',
            self::Disposed => 'Mise au rebut / Vendue',
            default => null,
        };
    }
}
