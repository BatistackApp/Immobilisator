<?php

namespace App\Filament\Resources\Assets\Widgets;

use App\Enums\AssetStatus;
use App\Models\AmortizationLine;
use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $currency = 'EUR';

        // Valeur Brute Totale (Actifs non cédés)
        $totalGrossValue = Asset::where('status', '!=', AssetStatus::Disposed)->sum('acquisition_value');

        // VNC Totale (Dernière ligne calculée pour chaque actif)
        $totalVnc = AmortizationLine::whereIn('id', function ($query) {
            $query->selectRaw('max(id)')->from('amortization_lines')->groupBy('asset_id');
        })->sum('book_value');

        // Nombre d'alertes maintenance
        $maintenanceAlerts = Asset::where('metadata->next_maintenance_date', '<=', now()->addDays(15))->count();

        return [
            Stat::make('Valeur Brute Globale', number_format($totalGrossValue, 2, ',', ' ').' €')
                ->description('Total des acquisitions actives')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Valeur Nette Comptable', number_format($totalVnc, 2, ',', ' ').' €')
                ->description('Valeur résiduelle totale du parc')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Alertes Maintenance', $maintenanceAlerts)
                ->description('Sous 15 jours')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color($maintenanceAlerts > 0 ? 'danger' : 'gray'),
        ];
    }
}
