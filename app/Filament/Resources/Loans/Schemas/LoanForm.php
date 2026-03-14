<?php

namespace App\Filament\Resources\Loans\Schemas;

use App\Enums\ProviderType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Détail du financement')
                    ->columnSpanFull()
                    ->description('Liez cet emprunt à une immobilisation et définissez les conditions bancaires.')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('asset_id')
                                ->label('Immobilisation')
                                ->relationship('asset', 'designation')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->helperText('L\'immobilisation financer par ce emprunt.'),

                            Select::make('provider_id')
                                ->label('Etablissement Bancaire')
                                ->relationship(
                                    'provider',
                                    'name',
                                    fn(Builder $query) => $query->where('type', ProviderType::Bank)
                                )
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('principal_amount')
                                ->label('Montant Principal')
                                ->numeric()
                                ->prefix('€')
                                ->required()
                                ->minValue(0),

                            TextInput::make('interest_rate')
                                ->label('Taux d\'intérêt')
                                ->numeric()
                                ->suffix('%')
                                ->required()
                                ->minValue(0)
                                ->step(0.01),

                            TextInput::make('duration_months')
                                ->label('Durée (Mois)')
                                ->numeric()
                                ->integer()
                                ->required()
                                ->minValue(1),

                            DatePicker::make('first_installment_date')
                                ->label('Date 1ere échéance')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                        ]),
                    ]),
            ]);
    }
}
