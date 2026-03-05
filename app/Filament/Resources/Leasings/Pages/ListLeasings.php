<?php

namespace App\Filament\Resources\Leasings\Pages;

use App\Filament\Resources\Leasings\LeasingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListLeasings extends ListRecords
{
    protected static string $resource = LeasingResource::class;
    protected static ?string $title = 'Liste des locations';
    protected static ?string $breadcrumb = 'Liste des locations';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvelle Location')->icon(Heroicon::Plus),
        ];
    }
}
