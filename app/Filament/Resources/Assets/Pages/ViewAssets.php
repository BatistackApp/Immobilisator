<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAssets extends ViewRecord
{
    protected static string $resource = AssetResource::class;
    protected static ?string $title = "Fiche d'une Immobilisation";
    protected static ?string $breadcrumb = 'Fiche';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
