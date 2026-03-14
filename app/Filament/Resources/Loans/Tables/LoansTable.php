<?php

namespace App\Filament\Resources\Loans\Tables;

use App\Enums\ProviderType;
use App\Models\Loan;
use App\Service\FinancingService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.reference')
                    ->label('Réf. Actif')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('asset.designation')
                    ->label('Immobilisation')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('provider.name')
                    ->label('Banque')
                    ->sortable(),

                TextColumn::make('principal_amount')
                    ->label('Montant')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('interest_rate')
                    ->label('Taux')
                    ->suffix('%')
                    ->badge()
                    ->color('info'),

                TextColumn::make('duration_months')
                    ->label('Durée')
                    ->suffix('mois'),

                TextColumn::make('first_installment_date')
                    ->label('début')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('provider_id')
                    ->label('Banque')
                    ->relationship('provider', 'name', fn (Builder $query) => $query->where('type', ProviderType::Bank)),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('view_schedule')
                    ->label('Échéancier')
                    ->icon('heroicon-o-table-cells')
                    ->color('gray')
                    ->modalHeading(fn (Loan $record) => "Tableau d'amortissement : {$record->asset->designation}")
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false)
                    ->modalContent(function (Loan $record, FinancingService $service) {
                        $schedule = $service->calculateLoanInstallments($record);

                        return new HtmlString(view('filament.components.loan-schedule', [
                            'schedule' => $schedule,
                            'loan' => $record,
                        ])->render());
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
