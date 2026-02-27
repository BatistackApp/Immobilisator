<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum InterventionType: string implements HasLabel
{
    case Repair = 'repair';         // Réparation (charge)
    case Improvement = 'improvement'; // Amélioration (incrémente la valeur brute)
    case Preventive = 'preventive';  // Entretien préventif

    public function getLabel(): string|Htmlable|null
    {
        return match($this) {
            self::Repair => 'Réparation',
            self::Improvement => 'Amélioration',
            self::Preventive => 'Entretien préventif',
            default => null,
        };
    }
}
