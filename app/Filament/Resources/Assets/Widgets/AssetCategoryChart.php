<?php

namespace App\Filament\Resources\Assets\Widgets;

use App\Enums\AssetStatus;
use DB;
use Filament\Widgets\ChartWidget;

class AssetCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Répartition de la Valeur Brute par Catégorie';

    protected function getData(): array
    {
        $data = DB::table('assets')
            ->join('asset_categories', 'assets.asset_category_id', '=', 'asset_categories.id')
            ->select('asset_categories.name', DB::raw('SUM(acquisition_value) as total_value'))
            ->where('status', '!=', AssetStatus::Disposed->value)
            ->whereNull('assets.deleted_at')
            ->groupBy('asset_categories.name')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Valeur Brute (€)',
                    'data' => $data->pluck('total_value')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', // Bleu
                        '#10b981', // Vert
                        '#f59e0b', // Ambre
                        '#ef4444', // Rouge
                        '#8b5cf6', // Violet
                        '#6366f1', // Indigo
                    ],
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    /**
     * Options de configuration du graphique (Chart.js).
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
