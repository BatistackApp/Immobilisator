<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Service\AmortizationService;
use App\Service\DisposalService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

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
                Action::make('dispose')
                    ->label('Céder')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->color('danger')
                    ->visible(fn (Asset $record) => $record->status !== AssetStatus::DISPOSED)
                    ->form([
                        DatePicker::make('disposal_date')
                            ->label('Date de Cession')
                            ->required()
                            ->default(now()),
                        TextInput::make('selling_price')
                            ->label('Prix de Vente')
                            ->numeric()
                            ->prefix('€')
                            ->default(0),
                    ])
                    ->action(function (Asset $record, array $data, DisposalService $service) {
                        $result = $service->processDisposal(
                            $record,
                            Carbon::parse($data['disposal_date']),
                            (float) $data['selling_price'],
                        );

                        Notification::make()
                            ->title('Actif cédé avec succès')
                            ->body('Plus/Moins-value : '.number_format($result['gain_loss'], 2).' €')
                            ->success()
                            ->send();
                    }),
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
