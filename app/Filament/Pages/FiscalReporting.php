<?php

namespace App\Filament\Pages;

use App\Models\Asset;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class FiscalReporting extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|null|\UnitEnum $navigationGroup = 'Reporting';

    protected static ?string $navigationLabel = 'États Fiscaux (2054/2055)';

    protected string $view = 'filament.pages.fiscal-reporting';

    public function table(Table $table): Table
    {
        $currentYear = Carbon::now()->year;

        return $table
            ->query(Asset::query())
            ->columns([
                TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable(),
                TextColumn::make('gross_value_opening')
                    ->label('Valeur Ouverture')
                    ->money('EUR'),
                TextColumn::make('augmentations')
                    ->label('Augmentations')
                    ->state(fn (Asset $record) => $record->interventions()->where('is_capitalized', true)->whereYear('intervention_date', $currentYear)->sum('cost'))
                    ->money('EUR')
                    ->color('success'),
                TextColumn::make('diminutions')
                    ->label('Diminutions')
                    ->state(fn (Asset $record) => $record->status->value === 'disposed' ? $record->acquisition_value : 0)
                    ->money('EUR')
                    ->color('danger'),
                TextColumn::make('valeur_cloture')
                    ->label('Valeur Clôture')
                    ->state(fn (Asset $record) => $record->gross_value_opening + $record->interventions()->where('is_capitalized', true)->sum('cost'))
                    ->money('EUR')
                    ->weight('bold'),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Exporter PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => // Logique d'export PDF...
                    null
                    ),
            ]);
    }
}
