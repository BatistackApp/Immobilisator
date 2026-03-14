<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;
    protected static ?string $title = 'Création d\'un emprunt';
    protected static ?string $breadcrumb = 'Création d\'un emprunt';
}
