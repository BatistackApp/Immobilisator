<?php

namespace App\Filament\Resources\Assets\Widgets;

use App\Enums\AssetStatus;
use App\Models\Asset;
use DB;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // 1. Valeur Brute Totale (simple sum)
        $totalGrossValue = Asset::where('status', '!=', AssetStatus::Disposed)
            ->sum('acquisition_value');

        // 2. VNC Totale Optimisée
        // Au lieu d'un whereIn avec une sous-requête complexe, on utilise une jointure
        // ou une agrégation directe sur la dernière ligne connue par actif.
        $totalVnc = DB::table('assets')
            ->join('amortization_lines', function ($join) {
                $join->on('assets.id', '=', 'amortization_lines.asset_id')
                    ->whereRaw('amortization_lines.id = (SELECT MAX(id) FROM amortization_lines WHERE asset_id = assets.id)');
            })
            ->whereNull('assets.deleted_at')
            ->where('assets.status', '!=', AssetStatus::Disposed->value)
            ->sum('amortization_lines.book_value');

        // 3. Alertes Maintenance
        $maintenanceAlerts = Asset::where('metadata->next_maintenance_date', '<=', now()->addDays(15))
            ->where('status', '!=', AssetStatus::Disposed)
            ->count();

        return [
            Stat::make('Valeur Brute Globale', number_format($totalGrossValue, 2, ',', ' ').' €')
                ->description('Total des acquisitions actives')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Valeur Nette Comptable', number_format($totalVnc, 2, ',', ' ').' €')
                ->description('VNC totale du parc actif')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Alertes Maintenance', $maintenanceAlerts)
                ->description('Sous 15 jours')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color($maintenanceAlerts > 0 ? 'danger' : 'gray'),
        ];
    }
}
