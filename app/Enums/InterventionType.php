<?php

namespace App\Enums;

enum InterventionType: string
{
    case Repair = 'repair';         // Réparation (charge)
    case Improvement = 'improvement'; // Amélioration (incrémente la valeur brute)
    case Preventive = 'preventive';  // Entretien préventif
}
