<?php

namespace App\Enums;

enum AmortizationMethod: string
{
    case Linear = 'linear';
    case Declining = 'declining';
}
