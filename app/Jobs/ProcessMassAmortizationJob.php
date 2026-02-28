<?php

namespace App\Jobs;

use App\Models\Asset;
use App\Service\AmortizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMassAmortizationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(AmortizationService $service): void
    {
        Asset::where('status', 'active')->chunk(100, function ($assets) use ($service) {
            foreach ($assets as $asset) {
                $service->generateSchedule($asset);
            }
        });
    }
}
