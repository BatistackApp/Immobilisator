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
        // Utilisation des Enums pour la lisibilité
        $totalGross = Asset::where('status', '!=', AssetStatus::Disposed)->sum('acquisition_value');

        // Idéalement, déplacer ce calcul complexe dans une classe de Service
        // Mais si on le garde ici, attention aux performances sur gros volumes.
        $totalVnc = DB::table('assets')
            ->join('amortization_lines', 'assets.id', '=', 'amortization_lines.asset_id')
            // Optimisation : Utiliser une jointure latérale ou une fenêtre si la DB le permet (Postgres/MySQL 8)
            // Sinon, s'assurer que l'amortissement est calculé correctement.
            ->where('assets.status', '!=', AssetStatus::Disposed->value)
            ->whereRaw('amortization_lines.year = (SELECT MAX(year) FROM amortization_lines WHERE asset_id = assets.id)')
            ->sum('amortization_lines.book_value');

        // Harmonisation à 30 jours pour la cohérence avec le tableau
        $alertDays = 30;
        $alerts = Asset::where('status', AssetStatus::Active)
            ->where('metadata->next_maintenance_date', '<=', now()->addDays($alertDays))
            // Exclure ce qui est déjà passé (retard) ou le compter différemment ?
            ->where('metadata->next_maintenance_date', '>=', now()->subYear())
            ->count();

        return [
            Stat::make('Valeur Brute (HT)', number_format($totalGross, 2, ',', ' ').' €') // Précision HT
                ->description('Investissement parc actif')
                ->color('success'),

            Stat::make('VNC Actuelle', number_format($totalVnc, 2, ',', ' ').' €')
                ->description('Valeur résiduelle comptable')
                ->color('primary'),

            Stat::make('Maintenances à venir', $alerts)
                ->description("Interventions sous $alertDays jours") // Texte dynamique
                ->color($alerts > 0 ? 'danger' : 'gray'),
        ];
    }
}
