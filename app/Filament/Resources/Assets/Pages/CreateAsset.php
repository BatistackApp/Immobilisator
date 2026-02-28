<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    protected static ?string $title = 'Nouvelle Immobilisation';

    protected static ?string $breadcrumb = 'Nouvelle Immobilisation';
}
