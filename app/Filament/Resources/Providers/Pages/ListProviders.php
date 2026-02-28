<?php

namespace App\Filament\Resources\Providers\Pages;

use App\Filament\Resources\Providers\ProviderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProviders extends ListRecords
{
    protected static string $resource = ProviderResource::class;

    protected static ?string $title = 'Liste des Tiers';

    protected static ?string $breadcrumb = 'Liste des Tiers';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Créer un nouveau')->icon('heroicon-s-plus'),
        ];
    }
}
