<?php

namespace App\Filament\Widgets;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class MaintenanceAlertsTable extends TableWidget
{
    protected static ?string $heading = 'Maintenances Préventives à venir';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->where('status', AssetStatus::Active)
                    ->whereNotNull('metadata->next_maintenance_date')
                    ->where('metadata->next_maintenance_date', '<=', now()->addDays(30))
                    ->orderBy('metadata->next_maintenance_date', 'asc')
            )
            ->emptyStateHeading('Aucune interventions ou maintenance de prévu actuellement')
            ->columns([
                TextColumn::make('metadata.next_maintenance_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color('danger')
                    ->weight('bold'),
                TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable(),
                TextColumn::make('designation')
                    ->label('Désignation'),
                TextColumn::make('location.name')
                    ->label('Localisation'),
                TextColumn::make('status')
                    ->label('Statut Actuel')
                    ->badge(),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Voir la fiche')
                    ->url(fn (Asset $record): string => "/admin/assets/{$record->id}/edit")
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
