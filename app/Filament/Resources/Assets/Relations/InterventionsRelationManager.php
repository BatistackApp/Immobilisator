<?php

namespace App\Filament\Resources\Assets\Relations;

use App\Enums\InterventionType;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InterventionsRelationManager extends RelationManager
{
    protected static string $relationship = 'interventions';

    protected static ?string $title = 'Historique des Interventions';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')->options(InterventionType::class)->required(),
            TextInput::make('title')->required()->label('Libellé'),
            TextInput::make('cost')->numeric()->prefix('€')->required(),
            DatePicker::make('intervention_date')->required(),
            Toggle::make('is_capitalized')
                ->label('Capitaliser (Augmente la valeur brute)')
                ->helperText('Si coché, l\'immo sera recalculée automatiquement.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('intervention_date')->label('Date')->date(),
                TextColumn::make('type')->label('Nature')->badge(),
                TextColumn::make('title')->label('Libellé'),
                TextColumn::make('cost')->label('Coût')->money('EUR'),
                IconColumn::make('is_capitalized')->label('Capitalisé')->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->label('Ajouter une intervention'),
            ]);
    }
}
