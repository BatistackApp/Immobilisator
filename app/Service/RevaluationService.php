<?php

namespace App\Service;

use App\Models\Asset;
use App\Models\Revaluation;
use DB;
use Illuminate\Support\Carbon;

class RevaluationService
{
    public function __construct(protected AmortizationService $amortizationService) {}

    /**
     * Applique une réévaluation à un actif.
     */
    public function revaluate(Asset $asset, Carbon $date, float $fairValue, ?string $expert = null, ?string $notes = null): Revaluation
    {
        return DB::transaction(function () use ($asset, $date, $fairValue, $expert, $notes) {
            // 1. Calculer la VNC actuelle à la date de réévaluation
            // On récupère la dernière ligne d'amortissement avant la date
            $lastLine = $asset->amortizationLines()
                ->where('year', '<', $date->year)
                ->orderBy('year', 'desc')
                ->first();

            $currentVnc = $lastLine ? $lastLine->book_value : $asset->acquisition_value;
            $gap = $fairValue - $currentVnc;

            // 2. Créer l'historique de réévaluation
            $revaluation = Revaluation::create([
                'asset_id' => $asset->id,
                'revaluation_date' => $date,
                'fair_value' => $fairValue,
                'previous_vnc' => $currentVnc,
                'gap_amount' => $gap,
                'expert_name' => $expert,
                'notes' => $notes,
            ]);

            // 3. Mettre à jour l'actif
            // La nouvelle valeur brute devient la juste valeur
            // La base amortissable est mise à jour pour le futur
            $asset->update([
                'acquisition_value' => $fairValue,
                'depreciable_basis' => $fairValue - $asset->salvage_value,
                'revaluation_surplus' => $asset->revaluation_surplus + $gap,
            ]);

            // 4. Recalculer le plan d'amortissement (Prospectivement)
            // Le service AmortizationService doit être configuré pour ne pas supprimer
            // les lignes is_posted = true.
            $this->amortizationService->generateSchedule($asset);

            return $revaluation;
        });
    }
}
