<?php

namespace App\Filament\Resources\AssetCategories\Schemas;

use App\Enums\AssetType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssetCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Générales')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Nom de la catégorie'),

                        Select::make('type')
                            ->options(AssetType::class)
                            ->required()
                            ->label('Nature'),

                        TextInput::make('default_useful_life')
                            ->numeric()
                            ->required()
                            ->label('Durée d\'utilité par défaut (ans)'),

                        Group::make()->schema([
                            TextInput::make('accounting_code_asset')
                                ->required()
                                ->label('Compte d\'Immo (Classe 2)'),

                            TextInput::make('accounting_code_depreciation')
                                ->required()
                                ->label('Compte d\'Amortissement (Classe 28)'),

                            TextInput::make('accounting_code_provision')
                                ->label('Compte de Provision (Classe 29)'),
                        ])->columns(3),
                    ])->columnSpan(2),
            ]);
    }
}
