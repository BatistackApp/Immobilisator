<?php

namespace App\Filament\Pages;

use App\Models\Asset;
use App\Service\FiscalExportService;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
            ->query(Asset::query()
                ->withSum(['interventions as augmentations' => function (Builder $query) use ($currentYear) {
                    $query->where('is_capitalized', true)
                        ->whereYear('intervention_date', $currentYear);
                }], 'cost'))
            ->columns([
                TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable(),
                TextColumn::make('gross_value_opening')
                    ->label('Valeur Ouverture')
                    ->money('EUR'),
                TextColumn::make('augmentations')
                    ->label('Augmentations')
                    ->state(fn (Asset $record) => $record->augmentations ?? 0)
                    ->money('EUR')
                    ->color('success'),
                TextColumn::make('diminutions')
                    ->label('Diminutions')
                    ->state(fn (Asset $record) => $record->status->value === 'disposed' ? $record->acquisition_value : 0)
                    ->money('EUR')
                    ->color('danger'),
                TextColumn::make('valeur_cloture')
                    ->label('Valeur Clôture')
                    ->state(fn (Asset $record) => $record->gross_value_opening + ($record->augmentations ?? 0))
                    ->money('EUR')
                    ->weight('bold'),
            ])
            ->headerActions([
                Action::make('export_2054')
                    ->label('Exporter 2054 (PDF)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (FiscalExportService $exportService) {
                        $year = Carbon::now()->year;
                        $pdfContent = $exportService->generateHtmlOutput($year);

                        return response()->streamDownload(fn () => print ($pdfContent), "liasse_2054_{$year}.pdf");
                    }),

                Action::make('export_2055')
                    ->label('Exporter 2055 (PDF)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (FiscalExportService $exportService) {
                        $year = Carbon::now()->year;
                        $pdfContent = $exportService->generate2055Pdf($year);

                        return response()->streamDownload(fn () => print ($pdfContent), "liasse_2055_{$year}.pdf");
                    }),
            ]);
    }
}
