<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLoan extends ViewRecord
{
    protected static string $resource = LoanResource::class;
    protected static ?string $title = 'Fiche d\'un emprunt';
    protected static ?string $breadcrumb = 'Fiche d\'un emprunt';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Editer'),
        ];
    }
}
