<?php

namespace App\Filament\Resources\AssetCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AssetCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Désignation')
                    ->searchable(),
                TextColumn::make('type')
                    ->alignCenter()
                    ->label('Type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('accounting_code_asset')
                    ->alignCenter()
                    ->label("Compte d'immobilisation")
                    ->badge()
                    ->searchable(),
                TextColumn::make('default_useful_life')
                    ->alignCenter()
                    ->label('Durée standard')
                    ->formatStateUsing(fn (string $state) => $state.' '.Str::plural('an', $state)),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()->label('Editer'),
                DeleteAction::make()
                    ->label('Supprimer')
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
