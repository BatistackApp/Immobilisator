<?php

use App\Enums\AmortizationMethod;
use App\Models\Asset;
use App\Service\AmortizationService;
use Illuminate\Support\Carbon;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(AmortizationService::class);
    $this->provider = \App\Models\Provider::factory()->create();
});

it('calcule correctement l\'amortissement linéaire sans prorata', function () {
    // Création d'un actif : 10 000€ sur 5 ans (20% / an)
    $asset = Asset::factory()->create([
        'acquisition_value' => 10000,
        'depreciable_basis' => 10000,
        'useful_life' => 5,
        'service_date' => Carbon::parse('2025-01-01'),
        'amortization_method' => AmortizationMethod::Linear,
        'provider_id' => $this->provider->id,
    ]);

    $this->service->generateSchedule($asset);

    // Vérification de la première annuité (2000€)
    $this->assertDatabaseHas('amortization_lines', [
        'asset_id' => $asset->id,
        'year' => 2025,
        'annuity_amount' => 2000,
        'accumulated_amount' => 2000,
        'book_value' => 8000,
    ]);

    // Vérification de la clôture du plan
    $lastLine = $asset->amortizationLines()->orderBy('year', 'desc')->first();

    expect((float) $lastLine->book_value)->toBe(8000.0)
        ->and((float) $lastLine->accumulated_amount)->toBe(2000.0);
});
