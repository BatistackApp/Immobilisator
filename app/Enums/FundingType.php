<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum FundingType: string implements HasLabel
{
    case OwnFunds = 'own_funds';   // Fonds propres
    case Leasing = 'leasing';       // Crédit-bail
    case Rental = 'rental';         // Location simple
    case Loan = 'loan';             // Emprunt

    public function getLabel(): string|Htmlable|null
    {
        return match($this) {
            self::OwnFunds => 'Fonds propres',
            self::Leasing => 'Crédit-bail',
            self::Rental => 'Location simple',
            self::Loan => 'Emprunt',
            default => null,
        };
    }
}
