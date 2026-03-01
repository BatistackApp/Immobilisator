<?php

namespace App\Filament\Pages;

use App\Models\AmortizationLine;
use App\Models\Asset;
use App\Service\AccountingExportService;
use App\Service\FiscalExportService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
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

    protected static ?int $navigationSort = 2;

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

                Action::make('export_accounting')
                    ->label('Générer les écritures (CSV)')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Exportation & Verrouillage')
                    ->modalDescription('Voulez-vous générer le fichier CSV des dotations pour l\'exercice '.$currentYear.' ? Attention : cette action marquera les lignes d\'amortissement comme "Comptabilisées" et les verrouillera contre toute modification future.')
                    ->action(function (AccountingExportService $service) use ($currentYear) {
                        // 1. On appelle le service pour générer le contenu (basé sur is_posted = false)
                        $csvContent = $service->generateDotationsCsv($currentYear);

                        // 2. On verrouille les lignes d'amortissement de l'année en cours
                        $updatedCount = AmortizationLine::where('year', $currentYear)
                            ->where('is_posted', false)
                            ->update(['is_posted' => true]);

                        if ($updatedCount > 0) {
                            Notification::make()
                                ->title('Exportation réussie')
                                ->body("$updatedCount lignes d'amortissement ont été marquées comme comptabilisées.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Aucune nouvelle donnée')
                                ->body("Toutes les lignes de l'exercice $currentYear ont déjà été comptabilisées.")
                                ->warning()
                                ->send();
                        }

                        // 3. On déclenche le téléchargement du fichier
                        return response()->streamDownload(
                            fn () => print ($csvContent),
                            "dotations_compta_{$currentYear}.csv"
                        );
                    }),
            ]);
    }
}
