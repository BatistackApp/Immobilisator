<?php

namespace App\Service;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\CompanySettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

/**
 * Service chargé de transformer les données Eloquent en structure
 * prête pour les tableaux fiscaux 2054 et 2055.
 */
class FiscalExportService
{
    /**
     * Prépare les données pour le tableau 2054 (Immobilisations).
     */
    public function get2054Data(int $year): Collection
    {
        return Asset::query()
            ->withSum(['interventions as augmentations' => function ($query) use ($year) {
                $query->where('is_capitalized', true)
                    ->whereYear('intervention_date', $year);
            }], 'cost')
            ->get()
            ->map(function ($asset) use ($year) {
                $diminutions = ($asset->status === AssetStatus::Disposed && $asset->updated_at->year === $year)
                    ? $asset->acquisition_value
                    : 0;

                return [
                    'reference' => $asset->reference,
                    'designation' => $asset->designation,
                    'valeur_debut' => $asset->gross_value_opening,
                    'augmentations' => $asset->augmentations ?? 0,
                    'diminutions' => $diminutions,
                    'valeur_fin' => $asset->gross_value_opening + ($asset->augmentations ?? 0) - $diminutions,
                    'category' => $asset->category->name,
                ];
            });
    }

    /**
     * Récupère les données préparées pour le tableau 2055 (Amortissements).
     */
    public function get2055Data(int $year): Collection
    {
        return Asset::query()
            ->with(['category', 'amortizationLines'])
            ->get()
            ->map(function ($asset) use ($year) {
                // 1. Amortissements au début : Opening + Somme des annuités des années précédentes
                $accumulatedBefore = $asset->amortizationLines
                    ->where('year', '<', $year)
                    ->sum('annuity_amount');

                $amortDebut = (float) $asset->accumulated_depreciation_opening + $accumulatedBefore;

                // 2. Dotations de l'exercice (Augmentations)
                $dotation = (float) $asset->amortizationLines
                    ->where('year', '==', $year)
                    ->sum('annuity_amount');

                // 3. Reprises sur sorties (Diminutions)
                // Si l'actif est sorti cette année, on récupère le cumul total à la date de sortie
                $reprise = 0;
                if ($asset->status === AssetStatus::Disposed && $asset->updated_at->year === $year) {
                    $reprise = (float) $asset->accumulated_depreciation_opening + $asset->amortizationLines->sum('annuity_amount');
                }

                return [
                    'reference' => $asset->reference,
                    'designation' => $asset->designation,
                    'category' => $asset->category?->name ?? 'Non classé',
                    'amort_debut' => $amortDebut,
                    'dotations' => $dotation,
                    'reprises' => $reprise,
                    'amort_fin' => $amortDebut + $dotation - $reprise,
                ];
            });
    }

    /**
     * Génère une vue HTML formatée pour le PDF ou l'impression.
     * Note: Dans un environnement Laravel réel, vous utiliseriez View::make()->render().
     */
    public function generateHtmlOutput(int $year): string
    {
        $assets = $this->get2054Data($year);
        $settings = CompanySettings::first();

        // On rend la vue Blade en HTML
        $html = View::make('pdf.2054', [
            'assets' => $assets,
            'settings' => $settings,
            'year' => $year,
        ])->render();

        // On utilise Browsershot pour convertir le HTML en PDF
        return Browsershot::html($html)
            ->format('A4')
            ->landscape()
            ->showBackground()
            ->margins(0, 0, 0, 0)
            ->waitUntilNetworkIdle() // Attend que le CDN Tailwind soit chargé
            ->pdf();
    }

    /**
     * Génère le PDF 2055 via Browsershot.
     */
    public function generate2055Pdf(int $year): string
    {
        $assets = $this->get2055Data($year);
        $settings = CompanySettings::first();

        $html = View::make('pdf.2055', [
            'assets' => $assets,
            'settings' => $settings,
            'year' => $year,
        ])->render();

        return Browsershot::html($html)
            ->format('A4')
            ->landscape()
            ->showBackground()
            ->margins(0, 0, 0, 0)
            ->waitUntilNetworkIdle()
            ->pdf();
    }
}
