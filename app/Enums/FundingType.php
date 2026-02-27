<?php

namespace App\Enums;

enum FundingType: string
{
    case OwnFunds = 'own_funds';   // Fonds propres
    case Leasing = 'leasing';       // Crédit-bail
    case Rental = 'rental';         // Location simple
    case Loan = 'loan';             // Emprunt
}
