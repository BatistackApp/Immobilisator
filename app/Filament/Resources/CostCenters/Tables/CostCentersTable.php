<?php

namespace App\Filament\Resources\CostCenters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CostCentersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Aucun poste analytique actuellement.')
            ->emptyStateDescription('Veuillez créer un nouveau poste analytique.')
            ->emptyStateActions([
                CreateAction::make()->label("Nouveau poste"),
            ])
            ->columns([
                TextColumn::make('code')
                    ->label('Code'),

                TextColumn::make('name')
                    ->label('Nom'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
