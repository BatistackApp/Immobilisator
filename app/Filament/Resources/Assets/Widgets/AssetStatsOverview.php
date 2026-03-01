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
        // 1. Valeur Brute (Actifs non cédés)
        $totalGross = Asset::where('status', '!=', AssetStatus::Disposed)->sum('acquisition_value');

        // 2. VNC Totale (Dernière ligne calculée par actif)
        $totalVnc = DB::table('amortization_lines')
            ->join('assets', 'amortization_lines.asset_id', '=', 'assets.id')
            ->where('assets.status', '!=', AssetStatus::Disposed->value) // Assure que seuls les actifs non cédés sont inclus
            ->whereIn('amortization_lines.id', function ($query) {
                $query->selectRaw('max(id)')->from('amortization_lines')->groupBy('asset_id');
            })
            ->sum('amortization_lines.book_value');

        // 3. Alertes (Maintenance + Fin d'amortissement)
        $alerts = Asset::where('status', AssetStatus::Active)
            ->where('metadata->next_maintenance_date', '<=', now()->addDays(15))
            ->count();

        return [
            Stat::make('Valeur Brute Totale', number_format($totalGross, 2, ',', ' ').' €')
                ->description('Coût historique du parc actif')
                ->color('success'),
            Stat::make('Valeur Nette (VNC)', number_format($totalVnc, 2, ',', ' ').' €')
                ->description('Reste à amortir')
                ->color('primary'),
            Stat::make('Alertes Maintenance', $alerts)
                ->description('Maintenances sous 15 jours')
                ->color($alerts > 0 ? 'danger' : 'gray'),
        ];
    }
}
