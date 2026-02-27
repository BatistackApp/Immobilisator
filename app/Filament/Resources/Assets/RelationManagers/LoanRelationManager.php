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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoanRelationManager extends RelationManager
{
    protected static string $relationship = 'loan';
    protected static ?string $title = 'Financement Bancaire';

    public function isReadOnly(): bool
    {
        return $this->getOwnerRecord()->funding_type !== FundingType::Loan;
    }

    protected static ?string $relatedResource = AssetResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Select::make('provider_id')
                    ->label('Banque / Organisme')
                    ->relationship('provider', 'name')
                    ->required(),
                TextInput::make('principal_amount')
                    ->label('Montant Emprunté')
                    ->numeric()
                    ->prefix('€')
                    ->required(),
                TextInput::make('interest_rate')
                    ->label('Taux d\'intérêt (%)')
                    ->numeric()
                    ->required(),
                TextInput::make('duration_months')
                    ->label('Durée (Mois)')
                    ->integer()
                    ->required(),
                DatePicker::make('first_installment_date')
                    ->label('Date 1ère échéance')
                    ->required(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider.name')->label('Banque'),
                TextColumn::make('principal_amount')->label('Capital')->money('EUR'),
                TextColumn::make('interest_rate')->label('Taux')->suffix(' %'),
                TextColumn::make('duration_months')->label('Durée'),
            ])
            ->headerActions([
                CreateAction::make()->hidden(fn () => $this->getOwnerRecord()->loan()->exists()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
