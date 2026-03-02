<?php

namespace App\Filament\Resources\Interventions\Pages;

use App\Filament\Resources\Interventions\InterventionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInterventions extends ListRecords
{
    protected static string $resource = InterventionResource::class;
    protected static ?string $title = 'Liste des interventions';
    protected static ?string $breadcrumb = 'Liste des interventions';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvelle intervention'),
        ];
    }
}
