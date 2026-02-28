<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum AmortizationMethod: string implements HasLabel
{
    case Linear = 'linear';
    case Declining = 'declining';

    public function getLabel(): string|Htmlable|null
    {
        return match($this) {
            self::Linear => 'Amortissement Linéaire',
            self::Declining => 'Amortissement Déclinant',
            default => null,
        };
    }
}
