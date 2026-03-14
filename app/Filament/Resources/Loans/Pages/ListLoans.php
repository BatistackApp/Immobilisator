<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLoans extends ListRecords
{
    protected static string $resource = LoanResource::class;
    protected static ?string $title = 'Emprunts / Financements';
    protected static ?string $breadcrumb = 'Emprunts / Financements';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Ajouter un emprunt'),
        ];
    }
}
