<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Enums\AmortizationMethod;
use App\Enums\AssetStatus;
use App\Enums\FundingType;
use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Asset Management')
                    ->tabs([
                        // ONGLET 1 : INFORMATIONS GÉNÉRALES
                        Tabs\Tab::make('Informations Générales')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('reference')
                                        ->label('Référence')
                                        ->required()
                                        ->unique(ignoreRecord: true),
                                    Forms\Components\TextInput::make('designation')
                                        ->label('Désignation')
                                        ->required()
                                        ->columnSpan(2),
                                    Forms\Components\Select::make('asset_category_id')
                                        ->label('Catégorie')
                                        ->relationship('category', 'name')
                                        ->required()
                                        ->searchable()
                                        ->preload(),
                                    Forms\Components\Select::make('location_id')
                                        ->label('Localisation')
                                        ->relationship('location', 'name')
                                        ->searchable()
                                        ->preload(),
                                    Forms\Components\Select::make('provider_id')
                                        ->label('Fournisseur d\'origine')
                                        ->relationship('provider', 'name')
                                        ->searchable(),
                                ]),
                            ]),

                        // ONGLET 2 : DONNÉES FINANCIÈRES
                        Tabs\Tab::make('Données Financières')
                            ->icon('heroicon-m-banknotes')
                            ->schema([
                                Grid::make(3)->schema([
                                    Forms\Components\Select::make('funding_type')
                                        ->label('Type de Financement')
                                        ->options(FundingType::class)
                                        ->required()
                                        ->reactive(),
                                    Forms\Components\TextInput::make('acquisition_value')
                                        ->label('Valeur d\'Acquisition')
                                        ->numeric()
                                        ->prefix('€')
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $set('depreciable_basis', (float) $state - (float) $get('salvage_value'))
                                        ),
                                    Forms\Components\TextInput::make('salvage_value')
                                        ->label('Valeur Résiduelle')
                                        ->numeric()
                                        ->prefix('€')
                                        ->default(0)
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set, $get) => $set('depreciable_basis', (float) $get('acquisition_value') - (float) $state)
                                        ),
                                    Forms\Components\TextInput::make('depreciable_basis')
                                        ->label('Base Amortissable')
                                        ->numeric()
                                        ->prefix('€')
                                        ->readOnly()
                                        ->helperText('Calculé automatiquement (Acquisition - Résiduelle)'),
                                    Forms\Components\DatePicker::make('acquisition_date')
                                        ->label('Date d\'Acquisition')
                                        ->required(),
                                    Forms\Components\DatePicker::make('service_date')
                                        ->label('Date de Mise en Service')
                                        ->required(),
                                    Forms\Components\TextInput::make('useful_life')
                                        ->label('Durée d\'Utilité (Années)')
                                        ->integer()
                                        ->required()
                                        ->minValue(1),
                                    Forms\Components\Select::make('amortization_method')
                                        ->label('Méthode d\'Amortissement')
                                        ->options(AmortizationMethod::class)
                                        ->required(),
                                    Forms\Components\Select::make('status')
                                        ->label('Statut')
                                        ->options(AssetStatus::class)
                                        ->default(AssetStatus::Active)
                                        ->required(),
                                ]),
                            ]),

                        // ONGLET 3 : ÉTATS FISCAUX (2054/2055)
                        Tabs\Tab::make('Paramètres Fiscaux')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('gross_value_opening')
                                        ->label('Valeur Brute Ouverture')
                                        ->numeric()
                                        ->prefix('€'),
                                    Forms\Components\TextInput::make('accumulated_depreciation_opening')
                                        ->label('Amortissements Ouverture')
                                        ->numeric()
                                        ->prefix('€'),
                                ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
