<?php

namespace App\Filament\Resources\Leasings\Pages;

use App\Filament\Resources\Leasings\LeasingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLeasing extends ViewRecord
{
    protected static string $resource = LeasingResource::class;
    protected static ?string $title = 'Fiche d\'une location';
    protected static ?string $breadcrumb = 'Fiche d\'une location';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
