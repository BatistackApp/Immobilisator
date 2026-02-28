<?php

namespace App\Filament\Resources\AssetCategories\Pages;

use App\Filament\Resources\AssetCategories\AssetCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssetCategories extends ListRecords
{
    protected static string $resource = AssetCategoryResource::class;
    protected static ?string $title = 'Liste des Catégories';
    protected static ?string $breadcrumb = 'Liste des Catégories';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvelle Catégorie')->icon('heroicon-s-plus'),
        ];
    }
}
