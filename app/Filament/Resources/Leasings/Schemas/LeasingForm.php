<?php

namespace App\Filament\Resources\Leasings\Schemas;

use App\Enums\AssetStatus;
use App\Enums\ProviderType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class LeasingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('asset_id')
                                ->label('Objet')
                                ->relationship('asset', 'designation', fn (Builder $query) => $query->where('status', '!=', AssetStatus::Disposed->value))
                                ->searchable()
                                ->required()
                                ->preload(),
                            TextInput::make('contract_number')
                                ->label('N° de Contrat')
                                ->required(),
                            Select::make('provider_id')
                                ->label('Bailleur')
                                ->relationship('provider', 'name', fn(Builder $query) => $query->where('type', ProviderType::Lessor->value))
                                ->required()
                                ->searchable()
                                ->preload(),
                            TextInput::make('monthly_rent')
                                ->label('Loyer Mensuel')
                                ->numeric()
                                ->prefix('€')
                                ->required(),
                            TextInput::make('purchase_option_price')
                                ->label('Option d\'achat')
                                ->numeric()
                                ->prefix('€')
                                ->required(),
                            DatePicker::make('start_date')
                                ->label('Début du contrat')
                                ->required(),
                            DatePicker::make('end_date')
                                ->label('Fin du contrat')
                                ->required(),
                            Toggle::make('option_exercised')
                                ->label('Option levée')
                                ->helperText('Cochez si vous avez racheté le bien à la fin du contrat.'),
                        ]),
                    ]),
            ]);
    }
}
