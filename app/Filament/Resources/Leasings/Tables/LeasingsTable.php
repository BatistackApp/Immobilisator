<?php

namespace App\Filament\Resources\Leasings\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LeasingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading("Aucune location")
            ->columns([
                TextColumn::make('contract_number')
                    ->label('N° de Contrat')
                    ->searchable(),

                TextColumn::make('provider.name')
                    ->label('Bailleur'),

                TextColumn::make('monthly_rent')
                    ->label('Loyer')
                    ->money('EUR'),

                TextColumn::make('end_date')
                    ->date()
                    ->label('Echéance'),

                IconColumn::make('option_exercised')->label('Racheté')->boolean(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
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
