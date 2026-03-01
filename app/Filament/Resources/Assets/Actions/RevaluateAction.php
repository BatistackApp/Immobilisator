<?php

namespace App\Filament\Resources\Assets\Actions;

use App\Models\Asset;
use App\Service\RevaluationService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class RevaluateAction
{
    public static function make(): Action
    {
        return Action::make('revaluate')
            ->label('Réévaluer l\'actif')
            ->icon('heroicon-o-arrow-trending-up')
            ->color('warning')
            ->modalHeading('Réévaluation de l\'immobilisation')
            ->modalDescription('Ajustez la valeur de l\'actif à sa juste valeur de marché. Cela recalculera les amortissements futurs.')
            ->schema([
                DatePicker::make('revaluation_date')
                    ->label('Date de réévaluation')
                    ->default(now())
                    ->required(),

                TextInput::make('fair_value')
                    ->label('Nouvelle juste valeur (Expertise)')
                    ->numeric()
                    ->prefix('€')
                    ->required()
                    ->helperText('La VNC actuelle sera comparée à ce montant pour calculer l\'écart.'),

                TextInput::make('expert_name')
                    ->label('Nom de l\'expert / Cabinet'),

                Textarea::make('notes')
                    ->label('Justification')
                    ->placeholder('Ex: Rapport d\'expertise foncière du 12/03/2025'),
            ])
            ->action(function (Asset $record, array $data, RevaluationService $service) {
                $service->revaluate(
                    $record,
                    Carbon::parse($data['revaluation_date']),
                    (float) $data['fair_value'],
                    $data['expert_name'],
                    $data['notes'],
                );

                Notification::make()
                    ->title('Réévaluation appliquée')
                    ->body('La base amortissable a été mise à jour et le plan d\'amortissement a été recalculé.')
                    ->success()
                    ->send();
            });
    }
}
