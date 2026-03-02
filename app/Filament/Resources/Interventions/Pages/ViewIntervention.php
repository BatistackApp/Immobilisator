<?php

namespace App\Filament\Resources\Interventions\Pages;

use App\Filament\Resources\Interventions\Actions\PrintAction;
use App\Filament\Resources\Interventions\InterventionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIntervention extends ViewRecord
{
    protected static string $resource = InterventionResource::class;
    protected static ?string $title = 'Fiche d\'une intervention';
    protected static ?string $breadcrumb = 'Fiche d\'une intervention';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            PrintAction::make(),
        ];
    }
}
