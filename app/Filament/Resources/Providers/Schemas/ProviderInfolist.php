<?php

namespace App\Filament\Resources\Providers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ProviderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité du Tiers')
                    ->description('Informations légales et classification.')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('name')
                                ->label('Raison Sociale')
                                ->weight('bold')
                                ->copyable(),

                            TextEntry::make('type')
                                ->label('Type de Tiers')
                                ->badge(),

                            TextEntry::make('tax_id')
                                ->label('SIRET / N° TVA')
                                ->placeholder('Non renseigné')
                                ->copyable(),
                        ]),
                    ]),

                Section::make('Coordonnées & Localisation')
                    ->description('Informations pour la prise de contact et la facturation.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('email')
                                ->label('Adresse Email')
                                ->icon('heroicon-m-envelope')
                                ->copyable()
                                ->placeholder('Aucun email'),

                            TextEntry::make('phone')
                                ->label('Téléphone')
                                ->icon('heroicon-m-phone')
                                ->placeholder('Aucun numéro'),

                            TextEntry::make('address')
                                ->label('Adresse Postale')
                                ->columnSpanFull()
                                ->formatStateUsing(function (Model $record) {
                                    return $record->address.'<br>'.$record->postal_code.' '.$record->city.'<br>'.$record->country;
                                })
                                ->html()
                                ->placeholder('Aucune adresse renseignée'),
                        ]),
                    ]),

                Section::make('Métadonnées')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('created_at')
                                ->label('Date de création')
                                ->dateTime('d/m/Y H:i'),

                            TextEntry::make('updated_at')
                                ->label('Dernière modification')
                                ->dateTime('d/m/Y H:i'),

                            TextEntry::make('user.last_login_at')
                                ->label('Dernière connexion')
                                ->dateTime('d/m/Y H:i'),
                        ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
