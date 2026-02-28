<?php

namespace App\Console\Commands;

use App\Models\Leasing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckLeasingExpiryCommand extends Command
{
    protected $signature = 'app:check-leasing-expiry';

    protected $description = 'Vérifie les contrats de crédit-bail finissant dans les 30 jours.';

    public function handle(): void
    {
        $upcomingExpiries = Leasing::with('asset')
            ->where('end_date', '<=', Carbon::now()->addDays(30))
            ->where('option_exercised', false)
            ->get();

        foreach ($upcomingExpiries as $leasing) {
            // Notification des gestionnaires (ici le premier utilisateur par défaut)
            $manager = User::first();

            if ($manager) {
                $manager->notify(new LeasingExpiryNotification($leasing));
                $this->info("Notification de fin de contrat envoyée pour : {$leasing->contract_number}");
            }

            $this->info("Le contrat {$leasing->contract_number} (Asset: {$leasing->asset->reference}) expire le {$leasing->end_date->format('d/m/Y')}.");
        }
    }
}
