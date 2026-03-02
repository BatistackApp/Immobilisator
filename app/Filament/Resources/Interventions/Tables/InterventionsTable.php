<?php

namespace App\Filament\Resources\Interventions\Tables;

use App\Enums\InterventionType;
use App\Filament\Resources\Interventions\Actions\PrintAction;
use App\Filament\Resources\Interventions\InterventionResource;
use App\Models\Intervention;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class InterventionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Aucune Intervention')
            ->emptyStateDescription('Vous pouvez ajouter une intervention en cliquant sur "Nouvelle Intervention"')
            ->emptyStateActions([
                CreateAction::make('create')
                    ->label('Nouvelle Intervention')
                    ->url(fn () => InterventionResource::getUrl('create')),
            ])
            ->columns([
                TextColumn::make('asset.designation')
                    ->label('Immobilisation')
                    ->searchable(),

                TextColumn::make('provider.name')
                    ->label('Intervenant')
                    ->searchable(),

                TextColumn::make('intervention_date')
                    ->label('Date d\'intervention')
                    ->date()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Désignation')
                    ->description(fn (Model $record) => $record->description),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('cost')
                    ->label('Montant')
                    ->money('EUR'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(InterventionType::class),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    PrintAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
