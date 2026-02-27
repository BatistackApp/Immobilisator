<?php

namespace App\Service;

use App\Enums\AmortizationMethod;
use App\Models\Asset;
use DB;

class AmortizationService
{
    /**
     * Génère ou recalcule le tableau d'amortissement complet d'un actif.
     */
    public function generateSchedule(Asset $asset): void
    {
        DB::transaction(function () use ($asset) {
            // Nettoyage des lignes non verrouillées avant recalcul
            $asset->amortizationLines()->where('is_posted', false)->delete();

            if ($asset->amortization_method === AmortizationMethod::Linear) {
                $this->calculateLinear($asset);
            } elseif ($asset->amortization_method === AmortizationMethod::Declining) {
                $this->calculateDeclining($asset);
            }
        });
    }

    /**
     * Logique de calcul Linéaire (Prorata temporis en jours).
     */
    private function calculateLinear(Asset $asset): void
    {
        $depreciableBasis = $asset->depreciable_basis;
        $usefulLife = $asset->useful_life;
        $annualRate = 1 / $usefulLife;

        $currentDate = $asset->service_date->copy();
        $accumulated = 0;
        $currentBaseValue = $depreciableBasis;

        // On boucle sur la durée de vie + 1 pour gérer le prorata de la 1ère année
        for ($yearIndex = 0; $yearIndex <= $usefulLife; $yearIndex++) {
            if ($accumulated >= $depreciableBasis) {
                break;
            }

            $yearEnd = $currentDate->copy()->endOfYear();

            // Calcul du prorata (base 360 jours fiscale)
            $daysInYear = 360;
            $daysUsed = min($daysInYear, $currentDate->diffInDays($yearEnd) + 1);

            $annuity = $depreciableBasis * $annualRate * ($daysUsed / $daysInYear);

            // Ajustement final pour ne pas dépasser la base amortissable
            if (($accumulated + $annuity) > $depreciableBasis || $yearIndex === $usefulLife) {
                $annuity = $depreciableBasis - $accumulated;
            }

            if ($annuity <= 0) {
                continue;
            }

            $accumulated += $annuity;

            $asset->amortizationLines()->create([
                'year' => $currentDate->year,
                'base_value' => $currentBaseValue,
                'annuity_amount' => $annuity,
                'accumulated_amount' => $accumulated,
                'book_value' => $depreciableBasis - $accumulated,
                'is_posted' => false,
            ]);

            $currentBaseValue -= $annuity;
            $currentDate = $yearEnd->addDay()->startOfDay();
        }
    }

    /**
     * Logique de calcul Dégressif (Prorata en mois complets).
     */
    private function calculateDeclining(Asset $asset): void
    {
        $depreciableBasis = $asset->depreciable_basis;
        $usefulLife = $asset->useful_life;

        $linearRate = 1 / $usefulLife;
        $coefficient = $this->getDecliningCoefficient($usefulLife);
        $degressiveRate = $linearRate * $coefficient;

        $currentDate = $asset->acquisition_date->copy()->startOfMonth();
        $accumulated = 0;
        $currentBaseValue = $depreciableBasis;

        $remainingYears = $usefulLife;

        while ($accumulated < $depreciableBasis && $remainingYears > 0) {
            $yearEnd = $currentDate->copy()->endOfYear();

            // Pivot : Si le taux linéaire sur la durée restante est > au taux dégressif
            $currentLinearRate = 1 / $remainingYears;
            $appliedRate = max($degressiveRate, $currentLinearRate);

            // Prorata 1ère année en mois
            $monthsInYear = 12;
            $monthsUsed = 13 - $currentDate->month; // De l'acquisition à la fin d'année

            if ($accumulated === 0.0) {
                $annuity = $currentBaseValue * $appliedRate * ($monthsUsed / $monthsInYear);
            } else {
                $annuity = $currentBaseValue * $appliedRate;
            }

            if (($accumulated + $annuity) > $depreciableBasis) {
                $annuity = $depreciableBasis - $accumulated;
            }

            $accumulated += $annuity;

            $asset->amortizationLines()->create([
                'year' => $currentDate->year,
                'base_value' => $currentBaseValue,
                'annuity_amount' => $annuity,
                'accumulated_amount' => $accumulated,
                'book_value' => $depreciableBasis - $accumulated,
                'is_posted' => false,
            ]);

            $currentBaseValue -= $annuity;
            $currentDate = $yearEnd->addDay()->startOfMonth();
            $remainingYears--;
        }
    }

    private function getDecliningCoefficient(int $years): float
    {
        return match (true) {
            $years <= 4 => 1.25,
            $years <= 6 => 1.75,
            default => 2.25,
        };
    }
}
