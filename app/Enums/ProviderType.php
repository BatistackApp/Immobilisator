<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ProviderType: string implements HasLabel
{
    case Supplier = 'supplier';
    case Bank = 'bank';
    case Lessor = 'lessor';

    public function getLabel(): string|Htmlable|null
    {
        return match($this) {
            self::Supplier => 'Fournisseur',
            self::Bank => 'Banque',
            self::Lessor => 'Bailleur',
        };
    }
}
