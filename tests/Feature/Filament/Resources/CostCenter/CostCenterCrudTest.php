<?php

use App\Models\Asset;
use App\Models\CostCenter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->actingAs($this->admin);
});

/**
 * Tests du CRUD CostCenter
 */
it('peut afficher la liste des centres de coûts', function () {
    CostCenter::create(['code' => 'TEST', 'name' => 'Test Center']);

    Livewire::test(\App\Filament\Resources\CostCenters\Pages\ListCostCenters::class)
        ->assertSuccessful()
        ->assertSee('TEST');
});

it('peut créer un nouveau centre de coût via Filament', function () {
    Livewire::test(\App\Filament\Resources\CostCenters\Pages\CreateCostCenter::class)
        ->fillForm([
            'code' => 'IT_DEP',
            'name' => 'Département Informatique',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('cost_centers', ['code' => 'IT_DEP']);
});

/**
 * Test de l'affectation dans le formulaire Asset
 */
it('permet d\'affecter un centre de coût à un actif dans le formulaire', function () {
    $costCenter = CostCenter::create(['code' => 'LOG', 'name' => 'Logistique']);
    $asset = Asset::factory()->create();

    Livewire::test(\App\Filament\Resources\Assets\Pages\EditAsset::class, [
        'record' => $asset->getRouteKey(),
    ])
        ->fillForm([
            'cost_center_id' => $costCenter->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($asset->refresh()->cost_center_id)->toBe($costCenter->id);
});
