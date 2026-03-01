<?php

use App\Models\Asset;
use App\Models\CostCenter;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('peut créer un centre de coût avec un code unique', function () {
    $costCenter = CostCenter::create([
        'code' => 'RD_PROD',
        'name' => 'Recherche et Développement',
    ]);

    expect($costCenter->code)->toBe('RD_PROD')
        ->and($costCenter->name)->toBe('Recherche et Développement');
});

it('possède une relation avec les actifs', function () {
    $costCenter = CostCenter::create(['code' => 'MKT', 'name' => 'Marketing']);

    $asset = Asset::factory()->create([
        'cost_center_id' => $costCenter->id,
    ]);

    expect($costCenter->assets)->toHaveCount(1)
        ->and($costCenter->assets->first()->id)->toBe($asset->id);
});

it('vérifie que l\'actif appartient à un centre de coût', function () {
    $costCenter = CostCenter::create(['code' => 'DIR', 'name' => 'Direction']);
    $asset = Asset::factory()->create(['cost_center_id' => $costCenter->id]);

    expect($asset->costCenter->code)->toBe('DIR');
});
