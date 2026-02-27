<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;
    protected static ?string $title = 'Liste des Immobilisations';
    protected static ?string $breadcrumb = 'Liste des Immobilisations';


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvelle Immobilisation')->icon('heroicon-s-plus'),
        ];
    }
}
