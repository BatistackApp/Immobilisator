<?php

namespace App\Filament\Resources\Interventions\Schemas;

use App\Enums\InterventionType;
use App\Enums\ProviderType;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class InterventionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Générales')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('asset_id')
                                    ->label('Immobilisation')
                                    ->relationship('asset', 'designation')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Select::make('provider_id')
                                    ->label('Intervenant')
                                    ->relationship('provider', 'name', function (Builder $query) {
                                        $query->where('type', ProviderType::Supplier->value);
                                    })
                                    ->searchable()
                                    ->preload(),

                                Select::make('type')
                                    ->label('Type')
                                    ->required()
                                    ->options(InterventionType::class)
                                    ->reactive(),
                            ]),

                        TextInput::make('title')
                            ->label('Titre')
                            ->required(),

                        Textarea::make('description')
                            ->label('Description'),
                    ]),

                Section::make('Intervention')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DateTimePicker::make('intervention_date')
                                    ->label('Date d\'intervention')
                                    ->seconds(false)
                                    ->minutesStep(30)
                                    ->required(),

                                TextInput::make('cost')
                                    ->label('Montant HT de l\'intervention')
                                    ->numeric()
                                    ->prefix('€')
                                    ->reactive()
                                    ->required(),

                                Checkbox::make('is_capitalized')
                                    ->inline()
                                    ->label("Capitalisation de l'intervention")
                                    ->hint("Si capitaliser, entre dans le calcul VNC de l'immobilisation"),
                            ]),
                    ]),
            ]);
    }
}
