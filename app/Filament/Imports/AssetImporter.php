<?php

namespace App\Filament\Imports;

use App\Models\Asset;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AssetImporter extends Importer
{
    protected static ?string $model = Asset::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('reference')
                ->label('Référence')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('designation')
                ->label('Désignation')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('category')
                ->label('Catégorie (ID)')
                ->relationship()
                ->requiredMapping(),

            ImportColumn::make('acquisition_value')
                ->label('Valeur d\'acquisition')
                ->numeric()
                ->rules(['required', 'numeric', 'min:0']),

            ImportColumn::make('service_date')
                ->label('Date de mise en service')
                ->rules(['required', 'date']),

            ImportColumn::make('useful_life')
                ->label('Durée d\'utilité (ans)')
                ->numeric()
                ->rules(['required', 'integer', 'min:1']),

            ImportColumn::make('gross_value_opening')
                ->label('Valeur Brute Ouverture (Optionnel)')
                ->numeric(),

            ImportColumn::make('accumulated_depreciation_opening')
                ->label('Amort. Ouverture (Optionnel)')
                ->numeric(),
        ];
    }

    public function resolveRecord(): Asset
    {
        return Asset::firstOrNew([
            'reference' => $this->data['reference'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'L\'importation des immobilisations est terminée et '.number_format($import->successful_rows).' '.str('ligne')->plural($import->successful_rows).' ont été importées.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('ligne')->plural($failedRowsCount).' ont échoué.';
        }

        return $body;
    }
}
