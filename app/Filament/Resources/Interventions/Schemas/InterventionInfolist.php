<?php

namespace App\Filament\Resources\Interventions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class InterventionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Détails de l\'Intervention')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')->label('Titre'),
                        TextEntry::make('intervention_date')->label('Date d\'intervention')->dateTime(),
                        TextEntry::make('type')->label('Type')->badge(),
                        TextEntry::make('cost')->label('Coût')->money('EUR'),
                        TextEntry::make('provider.name')->label('Intervenant'),
                        TextEntry::make('asset.designation')->label('Actif concerné'),
                        TextEntry::make('description')->label('Description')->columnSpanFull(),
                        TextEntry::make('is_capitalized')->label('Capitalisée')->formatStateUsing(fn (bool $state) => $state ? 'Oui' : 'Non'),
                        // Ajoutez d'autres champs pertinents ici, y compris l'invoice_path si vous voulez le rendre visible
                    ]),

                Section::make('Justificatif Correspondants')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('invoice_path')
                            ->formatStateUsing(function (string $state) {
                                $uri = config('app.url').'/'.$state;
                                return new HtmlString("<iframe src='{$uri}' frameborder='0' height='500' allowfullscreen></iframe>}'>");
                            }),
                    ])
            ]);
    }
}
