<?php

namespace App\Filament\Resources\AssetCategories\Pages;

use App\Filament\Resources\AssetCategories\AssetCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAssetCategory extends EditRecord
{
    protected static string $resource = AssetCategoryResource::class;

    protected static ?string $title = 'Editer Catégorie';

    protected static ?string $breadcrumb = 'Editer Catégorie';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
