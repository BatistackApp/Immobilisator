<?php

namespace App\Service;

use App\Enums\AssetStatus;
use App\Models\Asset;
use DB;
use Illuminate\Support\Carbon;

class DisposalService
{
    /**
     * Gère la sortie d'un actif et calcule l'annuité complémentaire.
     */
    public function processDisposal(Asset $asset, Carbon $disposalDate, float $sellingPrice = 0): array
    {
        return DB::transaction(function () use ($asset, $disposalDate, $sellingPrice) {
            // 1. Déterminer le début de l'exercice de sortie (simplifié au 01/01 ou date mise en service si même année)
            $startOfYear = $disposalDate->copy()->startOfYear();
            $calculationStart = $asset->service_date->gt($startOfYear) ? $asset->service_date : $startOfYear;

            // 2. Récupérer la dernière situation comptable validée
            $lastLine = $asset->amortizationLines()
                ->where('period_end', '<', $calculationStart)
                ->orderBy('year', 'desc')
                ->first();

            $baseValue = $lastLine ? $lastLine->book_value : $asset->depreciable_basis;
            $accumulatedBefore = $lastLine ? $lastLine->accumulated_amount : 0;

            // 3. Calcul de l'annuité complémentaire au prorata temporis (en jours)
            $daysInYear = 360;
            $daysUsed = min($daysInYear, $calculationStart->diffInDays($disposalDate) + 1);
            $annualRate = 1 / $asset->useful_life;

            // Note: En cas de dégressif, la règle de sortie peut varier, ici on applique le linéaire prorata
            $complementaryAnnuity = $asset->depreciable_basis * $annualRate * ($daysUsed / $daysInYear);

            // Sécurité : Ne pas amortir plus que la VNC restante
            if ($complementaryAnnuity > $baseValue) {
                $complementaryAnnuity = $baseValue;
            }

            // 4. Créer la ligne d'amortissement de sortie
            if ($complementaryAnnuity > 0) {
                $asset->amortizationLines()->create([
                    'year' => $disposalDate->year,
                    'base_value' => $baseValue,
                    'annuity_amount' => $complementaryAnnuity,
                    'accumulated_amount' => $accumulatedBefore + $complementaryAnnuity,
                    'book_value' => $baseValue - $complementaryAnnuity,
                    'is_posted' => false, // Sera posté lors de la clôture
                ]);
            }

            // 5. Calcul final de la VNC et du résultat de cession
            $finalVnc = $baseValue - $complementaryAnnuity;
            $gainLoss = $sellingPrice - $finalVnc;

            // 6. Mise à jour de l'actif
            $asset->update([
                'status' => AssetStatus::Disposed,
                'metadata' => array_merge($asset->metadata ?? [], [
                    'disposal_date' => $disposalDate->toDateString(),
                    'selling_price' => $sellingPrice,
                    'gain_loss' => $gainLoss,
                ])
            ]);

            return [
                'complementary_annuity' => $complementaryAnnuity,
                'vnc_at_disposal' => $finalVnc,
                'gain_loss' => $gainLoss,
                'is_gain' => $gainLoss >= 0,
            ];
        });
    }
}
