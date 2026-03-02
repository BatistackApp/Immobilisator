<?php

namespace App\Filament\Resources\Interventions\Pages;

use App\Filament\Resources\Interventions\InterventionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIntervention extends CreateRecord
{
    protected static string $resource = InterventionResource::class;
    protected static ?string $title = 'Création d\'une intervention';
    protected static ?string $breadcrumb = 'Création d\'une intervention';
}
