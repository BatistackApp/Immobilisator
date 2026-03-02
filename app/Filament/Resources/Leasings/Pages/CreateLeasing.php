<?php

namespace App\Filament\Resources\Leasings\Pages;

use App\Filament\Resources\Leasings\LeasingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeasing extends CreateRecord
{
    protected static string $resource = LeasingResource::class;
    protected static ?string $title = 'Création d\'une location';
    protected static ?string $breadcrumb = 'Création d\'une location';

}
