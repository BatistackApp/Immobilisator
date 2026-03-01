<?php

namespace App\Filament\Resources\CostCenters\Pages;

use App\Filament\Resources\CostCenters\CostCenterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCostCenters extends ListRecords
{
    protected static string $resource = CostCenterResource::class;
    protected static ?string $title = 'Liste des postes';
    protected static ?string $breadcrumb = 'Liste des postes';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouveau poste'),
        ];
    }
}
