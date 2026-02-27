<?php

namespace App\Console\Commands;

use App\Models\AmortizationLine;
use Illuminate\Console\Command;

class FiscalClosingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fiscal-closing {year : L’année à clôturer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Valide et verrouille toutes les annuités d’un exercice fiscal';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $year = $this->argument('year');

        $count = AmortizationLine::where('year', $year)
            ->where('is_posted', false)
            ->update(['is_posted' => true]);

        $this->info("Clôture réussie : {$count} lignes d'amortissement validées pour {$year}.");

        return self::SUCCESS;
    }
}
