<?php

namespace App\Filament\Resources\CostCenters\Pages;

use App\Filament\Resources\CostCenters\CostCenterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCostCenter extends EditRecord
{
    protected static string $resource = CostCenterResource::class;
    protected static ?string $title = 'Editer poste';
    protected static ?string $breadcrumb = 'Editer poste';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
