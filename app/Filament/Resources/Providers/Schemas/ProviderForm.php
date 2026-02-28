<?php

namespace App\Filament\Resources\Providers\Schemas;

use App\Enums\ProviderType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('name')->required()->label('Raison Social'),
                        Select::make('type')->options(ProviderType::class)->required()->label('Type de tiers'),
                        TextInput::make('tax_id')->label('N° TVA / SIRET'),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')->email()->label('Adresse Mail'),
                                TextInput::make('phone')->label('Téléphone')->tel(),
                            ]),

                        TextInput::make('address')->required()->label('Adresse')->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('postal_code')->required()->label('Code Postal'),
                                TextInput::make('city')->required()->label('Ville'),
                                TextInput::make('country')->required()->label('Pays'),
                            ]),
                    ]),
            ]);
    }
}
