<?php

namespace App\Console\Commands;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Notifications\AssetDepreciatedNotification;
use Illuminate\Console\Command;

class CheckDepreciatedAssetsCommand extends Command
{
    protected $signature = 'app:check-depreciated';

    protected $description = 'Vérifie les immobilisations arrivées à terme et notifie les gestionnaires.';

    public function handle(): void
    {
        // On récupère les actifs actifs dont la dernière ligne d'amortissement a une VNC de 0
        $assets = Asset::where('status', AssetStatus::Active)
            ->whereHas('amortizationLines', function ($query) {
                $query->where('book_value', '<=', 0);
            })->get();

        foreach ($assets as $asset) {
            // On notifie l'utilisateur (ou un groupe d'utilisateurs via un rôle)
            // Pour l'exemple, on notifie l'admin ou le premier user trouvé
            $admin = \App\Models\User::first();
            if ($admin) {
                $admin->notify(new AssetDepreciatedNotification($asset));
                $this->info("Notification envoyée pour l'immo : {$asset->reference}");
            }

            // On change le statut pour éviter de notifier en boucle
            $asset->update(['status' => AssetStatus::Disposed]);
        }
    }
}
