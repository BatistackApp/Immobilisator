<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Imports\AssetImporter;
use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Widgets\StatsOverviewWidget;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    protected static ?string $title = 'Liste des Immobilisations';

    protected static ?string $breadcrumb = 'Liste des Immobilisations';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvelle Immobilisation')->icon('heroicon-s-plus'),
            ImportAction::make()
                ->importer(AssetImporter::class)
                ->label('Importer des actifs')
                ->icon('heroicon-o-arrow-up-tray')
                ->modalHeading('Importer des actifs')
                ->modalDescription('Veuillez selectionner un fichiers csv formater afin d\'importer vos actifs dans le logiciel.')
                ->modalSubmitActionLabel('Importer'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
        ];
    }
}
