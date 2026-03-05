<?php

namespace App\Filament\Resources\Leasings\Pages;

use App\Filament\Resources\Leasings\LeasingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLeasing extends EditRecord
{
    protected static string $resource = LeasingResource::class;
    protected static ?string $title = 'Edition d\'une location';
    protected static ?string $breadcrumb = 'Edition d\'une location';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
