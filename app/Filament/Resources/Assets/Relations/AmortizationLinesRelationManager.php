<?php

namespace App\Filament\Resources\Assets\Relations;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AmortizationLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'amortizationLines';

    protected static ?string $title = 'Tableau d\'amortissement';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year')->label('Année')->sortable(),
                TextColumn::make('base_value')->label('Base Calcul')->money('EUR'),
                TextColumn::make('annuity_amount')->label('Annuité')->money('EUR'),
                TextColumn::make('accumulated_amount')->label('Cumul Amort.')
                    ->color('warning')
                    ->money('EUR'),
                TextColumn::make('book_value')->label('VNC (Valeur Nette)')
                    ->color('success')
                    ->weight('bold')
                    ->money('EUR'),
                IconColumn::make('is_posted')
                    ->label('Comptabilisé')
                    ->boolean(),
            ])
            ->defaultSort('year', 'asc');
    }
}
