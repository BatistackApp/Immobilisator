<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLoan extends EditRecord
{
    protected static string $resource = LoanResource::class;
    protected static ?string $title = 'Edition d\'un emprunt';
    protected static ?string $breadcrumb = 'Edition d\'un emprunt';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Fiche'),
            DeleteAction::make()->label('Supprimer'),
            ForceDeleteAction::make()->label('Annuler'),
            RestoreAction::make()->label('Restaurer'),
        ];
    }
}
