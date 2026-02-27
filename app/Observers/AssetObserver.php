<?php

namespace App\Observers;

use App\Models\Asset;
use App\Service\AmortizationService;

class AssetObserver
{
    public function __construct(protected AmortizationService $service) {}

    public function created(Asset $asset): void
    {
        // On génère automatiquement le tableau d'amortissement à la création
        $this->service->generateSchedule($asset);
    }

    public function updated(Asset $asset): void
    {
        // Si la valeur d'acquisition ou la durée de vie change, on recalcule
        if ($asset->wasChanged(['acquisition_value', 'useful_life', 'service_date', 'amortization_method'])) {
            $this->service->generateSchedule($asset);
        }
    }
}
