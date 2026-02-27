<?php

namespace App\Service;

use App\Models\Asset;

class FiscalReportingService
{
    public function getTableau2054Data(int $fiscalYear): array
    {
        return Asset::with(['interventions' => fn ($q) => $q->where('is_capitalized', true)])
            ->get()
            ->map(function ($asset) use ($fiscalYear) {
                $augmentations = $asset->interventions
                    ->filter(fn ($i) => $i->intervention_date->year === $fiscalYear)
                    ->sum('cost');

                return [
                    'reference' => $asset->reference,
                    'designation' => $asset->designation,
                    'valeur_ouverture' => $asset->gross_value_opening,
                    'augmentations' => $augmentations,
                    'diminutions' => $asset->status->value === 'disposed' ? $asset->acquisition_value : 0,
                    'valeur_cloture' => $asset->gross_value_opening + $augmentations,
                ];
            })->toArray();
    }
}
