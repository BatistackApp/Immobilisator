<?php

namespace App\Filament\Widgets;

use App\Models\AmortizationLine;
use DB;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class AssetEvolutionChart extends ChartWidget
{
    protected ?string $heading = 'Évolution de la Valeur Nette Comptable (VNC)';

    protected static ?int $sort = 2;

    public ?Model $record = null;

    protected function getData(): array
    {
        // On utilise SUM(book_value) pour la compatibilité avec ONLY_FULL_GROUP_BY.
        // Pour un actif seul, la somme d'une ligne par année retournera la valeur correcte.
        $query = AmortizationLine::query()
            ->select('year', DB::raw('SUM(book_value) as total_vnc'))
            ->when($this->record, fn ($q) => $q->where('asset_id', $this->record->id))
            ->groupBy('year')
            ->orderBy('year');

        // Pour la vue globale, on limite aux 10 prochaines années
        // Pour un actif seul, on affiche toute sa durée de vie calculée
        if (! $this->record) {
            $query->where('year', '>=', now()->year)->limit(10);
        }

        $data = $query->get();

        return [
            'datasets' => [
                [
                    'label' => $this->record ? "VNC de l'actif ({$this->record->reference})" : 'VNC Globale du Parc (€)',
                    'data' => $data->pluck('total_vnc')->toArray(),
                    'fill' => 'start',
                    'borderColor' => $this->record ? '#10b981' : '#3b82f6', // Vert pour un actif, Bleu pour le global
                    'backgroundColor' => $this->record ? 'rgba(16, 185, 129, 0.1)' : 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $data->pluck('year')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
