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
            // 1. Déterminer le début de l'exercice de sortie (01/01 de l'année de sortie)
            $startOfYear = $disposalDate->copy()->startOfYear();

            // Le calcul commence soit au 01/01, soit à la mise en service si c'est plus récent
            $calculationStart = $asset->service_date->gt($startOfYear)
                ? $asset->service_date
                : $startOfYear;

            // 2. Récupérer la dernière situation comptable validée (Année N-1)
            // Correction ici : On utilise 'year' au lieu de 'period_end'
            $lastLine = $asset->amortizationLines()
                ->where('year', '<', $disposalDate->year)
                ->orderBy('year', 'desc')
                ->first();

            // Si aucune ligne n'existe, on repart de la base d'origine
            $baseValue = $lastLine ? $lastLine->book_value : $asset->depreciable_basis;
            $accumulatedBefore = $lastLine ? $lastLine->accumulated_amount : 0;

            // 3. Calcul de l'annuité complémentaire au prorata temporis (Base 360 jours)
            // Calcul du nombre de jours entre le début de l'exercice et la sortie
            $daysInYear = 360;
            $daysUsed = min($daysInYear, $calculationStart->diffInDays($disposalDate) + 1);
            $annualRate = 1 / $asset->useful_life;

            $complementaryAnnuity = $asset->depreciable_basis * $annualRate * ($daysUsed / $daysInYear);

            // Sécurité : Ne pas amortir plus que la Valeur Nette Comptable restante
            if ($complementaryAnnuity > $baseValue) {
                $complementaryAnnuity = $baseValue;
            }

            // 4. Création de la ligne d'amortissement finale pour l'année de sortie
            if ($complementaryAnnuity > 0) {
                $asset->amortizationLines()->create([
                    'year' => $disposalDate->year,
                    'base_value' => $baseValue,
                    'annuity_amount' => $complementaryAnnuity,
                    'accumulated_amount' => $accumulatedBefore + $complementaryAnnuity,
                    'book_value' => $baseValue - $complementaryAnnuity,
                    'is_posted' => false,
                ]);
            }

            // 5. Calcul du résultat de cession (Prix de vente - VNC finale)
            $finalVnc = $baseValue - $complementaryAnnuity;
            $gainLoss = $sellingPrice - $finalVnc;

            // 6. Mise à jour du statut et stockage des métadonnées de sortie
            $asset->update([
                'status' => AssetStatus::Disposed,
                'metadata' => array_merge($asset->metadata ?? [], [
                    'disposal_date' => $disposalDate->toDateString(),
                    'selling_price' => $sellingPrice,
                    'gain_loss' => $gainLoss,
                    'vnc_at_disposal' => $finalVnc,
                ])
            ]);

            return [
                'complementary_annuity' => $complementaryAnnuity,
                'vnc_at_disposal' => $finalVnc,
                'gain_loss' => $gainLoss,
            ];
        });
    }
}
