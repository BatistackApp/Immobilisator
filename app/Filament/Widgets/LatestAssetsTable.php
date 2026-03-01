<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestAssetsTable extends TableWidget
{
    protected static ?string $heading = 'Dernières Acquisitions';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Asset::query()->limit(5))
            ->columns([
                TextColumn::make('acquisition_date')->label('Date')->date('d/m/Y'),
                TextColumn::make('reference')->label('Référence')->searchable(),
                TextColumn::make('designation')->label('Désignation'),
                TextColumn::make('category.name')->label('Catégorie'),
                TextColumn::make('acquisition_value')->label('Valeur HT')->money('EUR'),
                TextColumn::make('status')->label('Statut')->badge(),
            ]);
    }
}
