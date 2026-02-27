<?php

namespace App\Observers;

use App\Enums\InterventionType;
use App\Models\Intervention;
use App\Service\AmortizationService;
use Illuminate\Support\Carbon;

class InterventionObserver
{
    public function __construct(protected AmortizationService $service) {}

    public function created(Intervention $intervention): void
    {
        if ($intervention->is_capitalized) {
            $this->recalculateAsset($intervention);
        }

        if ($intervention->type === InterventionType::Preventive) {
            $this->scheduleNextMaintenance($intervention);
        }
    }

    public function updated(Intervention $intervention): void
    {
        if ($intervention->wasChanged(['is_capitalized', 'cost'])) {
            $this->recalculateAsset($intervention);
        }
    }

    protected function recalculateAsset(Intervention $intervention): void
    {
        $asset = $intervention->asset;

        // Mise à jour de la base amortissable de l'asset
        $newBasis = $asset->acquisition_value + $asset->interventions()->where('is_capitalized', true)->sum('cost');
        $asset->update(['depreciable_basis' => $newBasis]);

        // Déclenchement du recalcul du plan d'amortissement
        $this->service->generateSchedule($asset);
    }

    /**
     * Met à jour la date de prochaine maintenance dans le champ JSON metadata.
     */
    protected function scheduleNextMaintenance(Intervention $intervention): void
    {
        $asset = $intervention->asset;
        $metadata = $asset->metadata ?? [];

        // On définit la prochaine maintenance (ex: dans 6 mois par défaut, ou selon une logique métier)
        $nextDate = Carbon::parse($intervention->intervention_date)->addMonths(6);

        $metadata['last_preventive_maintenance'] = $intervention->intervention_date->toDateString();
        $metadata['next_maintenance_date'] = $nextDate->toDateString();

        $asset->update(['metadata' => $metadata]);
    }
}
