<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\User;
use App\Notifications\MaintenanceDueNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckMaintenanceCommand extends Command
{
    protected $signature = 'app:check-maintenance';

    protected $description = 'Vérifie les actifs nécessitant une maintenance préventive sous 7 jours.';

    public function handle(): void
    {
        $targetDate = Carbon::now()->addDays(7)->toDateString();

        // On cherche dans le JSON metadata la clé next_maintenance_date
        $assets = Asset::where('metadata->next_maintenance_date', '<=', $targetDate)
            ->where('status', '!=', \App\Enums\AssetStatus::Disposed)
            ->get();

        if ($assets->isEmpty()) {
            $this->info("Aucune maintenance à prévoir pour l'instant.");

            return;
        }

        $manager = User::first(); // À adapter selon vos besoins de rôles

        foreach ($assets as $asset) {
            $dueDate = $asset->metadata['next_maintenance_date'];

            if ($manager) {
                $manager->notify(new MaintenanceDueNotification($asset, $dueDate));
                $this->info("Alerte de maintenance envoyée pour : {$asset->reference}");
            }
        }
    }
}
