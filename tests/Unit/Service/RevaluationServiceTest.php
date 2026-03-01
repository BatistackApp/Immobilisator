<?php

use App\Models\AmortizationLine;
use App\Models\Asset;
use App\Models\Revaluation;
use App\Service\AmortizationService;
use App\Service\RevaluationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    // On mock le service d'amortissement car on veut tester la logique de réévaluation
    // sans dépendre de la complexité du moteur de calcul complet.
    $this->amortizationService = $this->mock(AmortizationService::class);
    $this->service = new RevaluationService($this->amortizationService);
});

it('calcule correctement l\'écart de réévaluation pour un nouvel actif', function () {
    $asset = Asset::factory()->create([
        'acquisition_value' => 10000,
        'depreciable_basis' => 10000,
        'service_date' => Carbon::parse('2025-01-01'),
    ]);

    // On s'attend à ce que le plan soit régénéré
    $this->amortizationService->shouldReceive('generateSchedule')->once()->with($asset);

    $date = Carbon::parse('2025-06-01');
    $fairValue = 15000;

    $revaluation = $this->service->revaluate($asset, $date, $fairValue);

    // Vérification de l'écart (15000 - 10000 = 5000)
    expect($revaluation->gap_amount)->toBe(5000.0)
        ->and($revaluation->previous_vnc)->toBe(10000.0);

    // Vérification de la mise à jour de l'actif
    $asset->refresh();
    expect((float) $asset->acquisition_value)->toBe(15000.0)
        ->and((float) $asset->revaluation_surplus)->toBe(5000.0);
});

it('calcule l\'écart par rapport à la dernière VNC enregistrée', function () {
    $asset = Asset::factory()->create(['acquisition_value' => 10000]);

    // On simule une année d'amortissement déjà passée
    AmortizationLine::create([
        'asset_id' => $asset->id,
        'year' => 2024,
        'base_value' => 10000,
        'annuity_amount' => 2000,
        'accumulated_amount' => 2000,
        'book_value' => 8000, // VNC de départ pour la réévaluation
        'is_posted' => true,
    ]);

    $this->amortizationService->shouldReceive('generateSchedule')->once();

    // Réévaluation en 2025 à 12 000€
    $revaluation = $this->service->revaluate($asset, Carbon::parse('2025-02-01'), 12000);

    // Écart : 12000 (Fair) - 8000 (VNC) = 4000
    expect($revaluation->gap_amount)->toBe(4000.0)
        ->and($revaluation->previous_vnc)->toBe(8000.0);
});

it('gère une réévaluation à la baisse (dépréciation exceptionnelle)', function () {
    $asset = Asset::factory()->create(['acquisition_value' => 10000]);
    $this->amortizationService->shouldReceive('generateSchedule')->once();

    // Expertise à 7 000€
    $revaluation = $this->service->revaluate($asset, Carbon::now(), 7000);

    expect($revaluation->gap_amount)->toBe(-3000.0)
        ->and($asset->refresh()->revaluation_surplus)->toBe(-3000.0);
});

it('annule tout en cas d\'erreur de calcul (Transaction)', function () {
    $asset = Asset::factory()->create(['acquisition_value' => 10000]);

    // On force une erreur dans le service d'amortissement
    $this->amortizationService->shouldReceive('generateSchedule')
        ->andThrow(new \Exception('Erreur critique de calcul'));

    try {
        $this->service->revaluate($asset, Carbon::now(), 20000);
    } catch (\Exception $e) {
        // L'exception est attendue
    }

    // L'actif ne doit pas avoir été mis à jour à cause du rollback
    expect($asset->refresh()->acquisition_value)->toBe(10000.0)
        ->and(Revaluation::count())->toBe(0);
});
