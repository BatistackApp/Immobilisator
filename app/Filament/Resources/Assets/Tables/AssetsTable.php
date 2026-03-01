<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetStatus;
use App\Filament\Resources\Assets\Actions\DisposeAction;
use App\Models\Asset;
use App\Service\AmortizationService;
use App\Service\AssetLabelService;
use App\Service\DisposalService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->label('Réf.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('designation')
                    ->label('Désignation')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Catégorie'),
                TextColumn::make('acquisition_value')
                    ->label('Valeur Brute')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),
                TextColumn::make('service_date')
                    ->label('Mise en service')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options(AssetStatus::class),
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                // ACTION : RECALCULER LE PLAN
                Action::make('recalculate')
                    ->label('Recalculer')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Asset $record, AmortizationService $service) {
                        $service->generateSchedule($record);
                        Notification::make()
                            ->title('Tableau d\'amortissement mis à jour')
                            ->success()
                            ->send();
                    }),

                // ACTION : CESSION / SORTIE
                DisposeAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    BulkAction::make('print_label')
                        ->label('Imprimer Étiquettes (PDF)')
                        ->icon('heroicon-o-qr-code')
                        ->action(function (Collection $records, AssetLabelService $service) {
                            $pdf = $service->generateLabelsPdf($records);

                            return response()->streamDownload(fn () => print ($pdf), 'etiquettes_inventaire.pdf');
                        }),
                ]),
            ]);
    }
}
