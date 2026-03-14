<?php

namespace App\Filament\Resources\Loans\Schemas;

use App\Filament\Infolists\Components\LoanScheduleEntry;
use App\Service\FinancingService;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LoanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité du Financement')
                    ->icon('heroicon-m-identification')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('asset.designation')
                                ->label('Immobilisation financée')
                                ->weight('bold')
                                ->color('primary'),

                            TextEntry::make('provider.name')
                                ->label('Établissement Bancaire')
                                ->icon('heroicon-m-building-library'),

                            TextEntry::make('asset.reference')
                                ->label('Référence Interne')
                                ->badge(),
                        ]),
                    ]),

                // SECTION 2 : CONDITIONS FINANCIÈRES
                Section::make('Conditions du Prêt')
                    ->icon('heroicon-m-banknotes')
                    ->description('Détails contractuels de l\'emprunt bancaire.')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('principal_amount')
                                ->label('Capital Emprunté')
                                ->money('EUR')
                                ->weight('black'),

                            TextEntry::make('interest_rate')
                                ->label('Taux Annuel')
                                ->suffix(' %')
                                ->color('info'),

                            TextEntry::make('duration_months')
                                ->label('Durée Totale')
                                ->suffix(' mois'),

                            TextEntry::make('first_installment_date')
                                ->label('Première Échéance')
                                ->date('d/m/Y'),
                        ]),
                    ]),

                // SECTION 3 : ÉCHÉANCIER DYNAMIQUE
                Section::make('Tableau d\'Amortissement Financier')
                    ->icon('heroicon-m-table-cells')
                    ->collapsible()
                    ->schema([
                        // On réutilise ici la vue du Canvas pour un affichage intégré
                        LoanScheduleEntry::make('schedule')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
