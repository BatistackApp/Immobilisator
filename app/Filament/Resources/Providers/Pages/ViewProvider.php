<?php

namespace App\Filament\Resources\Providers\Pages;

use App\Filament\Resources\Providers\ProviderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProvider extends ViewRecord
{
    protected static string $resource = ProviderResource::class;
    protected static ?string $title = "Fiche d'un Tier";
    protected static ?string $breadcrumb = 'Fiche';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
