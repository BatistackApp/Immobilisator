<?php

namespace App\Filament\Resources\Interventions\Actions;

use App\Models\Intervention;
use App\Service\FiscalExportService;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class PrintAction
{
    public static function make(): Action
    {
        return Action::make('print')
            ->icon(Heroicon::OutlinedPrinter)
            ->color('primary')
            ->label('Imprimer')
            ->action(function (Intervention $record, FiscalExportService $exportService) {
                $pdfContent = $exportService->generateInterventionReportPdf($record);

                return response()->streamDownload(
                    fn () => print ($pdfContent),
                    "intervention_{$record->id}.pdf",
                );
            });
    }
}
