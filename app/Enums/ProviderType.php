<?php

namespace App\Enums;

enum ProviderType: string
{
    case Supplier = 'supplier';
    case Bank = 'bank';
    case Lessor = 'lessor';
}
