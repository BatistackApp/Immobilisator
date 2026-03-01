<?php

namespace App\Filament\Resources\Assets\Actions;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Service\DisposalService;
use App\Service\FiscalExportService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class DisposeAction
{
    public static function make(): Action
    {
        return Action::make('dispose')
            ->label('Sortir du bilan')
            ->icon('heroicon-o-archive-box-arrow-down')
            ->color('danger')
            ->hidden(fn (Asset $record) => $record->status === AssetStatus::Disposed)
            ->modalHeading('Procéder à la sortie de l\'actif')
            ->modalDescription('Cette action va calculer l\'annuité finale et déterminer le résultat de cession.')
            ->schema([
                DatePicker::make('disposal_date')
                    ->label('Date de sortie')
                    ->default(now())
                    ->required()
                    ->maxDate(now()),

                TextInput::make('selling_price')
                    ->label('Prix de cession (HT)')
                    ->numeric()
                    ->prefix('€')
                    ->default(0)
                    ->helperText('Laissez à 0 en cas de mise au rebut.'),

                Textarea::make('reason')
                    ->label('Motif de la sortie')
                    ->placeholder('Vente, vol, casse, obsolescence...')
                    ->rows(2),
            ])
            ->action(function (Asset $record, array $data, DisposalService $disposalService, FiscalExportService $fiscalService) {
                // 1. Traitement comptable (Calcul annuité prorata + VNC)
                $disposalService->processDisposal(
                    $record,
                    Carbon::parse($data['disposal_date']),
                    (float) $data['selling_price']
                );

                // 2. Mise à jour manuelle des métadonnées pour le motif (non géré par le service par défaut)
                $record->refresh();
                $record->update([
                    'metadata' => array_merge($record->metadata ?? [], [
                        'disposal_reason' => $data['reason'],
                    ]),
                ]);

                // 3. Notification de succès
                Notification::make()
                    ->title('Actif sorti du bilan')
                    ->success()
                    ->send();

                // 4. Génération et téléchargement automatique du certificat PDF
                $pdfContent = $fiscalService->generateDisposalCertificatePdf($record);

                return response()->streamDownload(
                    fn () => print ($pdfContent),
                    "certificat_sortie_{$record->reference}.pdf"
                );
            });
    }
}
