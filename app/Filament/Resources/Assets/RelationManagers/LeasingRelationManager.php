<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use App\Enums\FundingType;
use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeasingRelationManager extends RelationManager
{
    protected static string $relationship = 'leasing';
    protected static ?string $title = 'Contrat de Crédit-Bail';

    protected static ?string $relatedResource = AssetResource::class;

    public function isReadOnly(): bool
    {
        // On n'affiche le leasing que si le type de financement de l'actif est 'leasing'
        return $this->getOwnerRecord()->funding_type !== FundingType::Leasing;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('contract_number')
                        ->label('N° de Contrat')
                        ->required(),
                    Select::make('provider_id')
                        ->label('Bailleur')
                        ->relationship('provider', 'name')
                        ->required()
                        ->searchable(),
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contract_number')->label('N° Contrat'),
                TextColumn::make('provider.name')->label('Bailleur'),
                TextColumn::make('monthly_rent')->label('Loyer')->money('EUR'),
                TextColumn::make('end_date')->label('Échéance')->date(),
                IconColumn::make('option_exercised')->label('Racheté')->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->hidden(fn () => $this->getOwnerRecord()->leasing()->exists()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
