<?php

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use App\Service\AmortizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(AmortizationService::class);
    $this->provider = \App\Models\Provider::factory()->create();
});

it('permet à un utilisateur authentifié de créer une immobilisation', function () {
    $category = AssetCategory::factory()->create();
    $user = User::factory()->create();

    $payload = [
        'asset_category_id' => $category->id,
        'reference' => 'IMM-PEST-001',
        'designation' => 'Station de travail Precision',
        'funding_type' => 'own_funds',
        'acquisition_value' => 3000,
        'depreciable_basis' => 3000,
        'acquisition_date' => '2025-01-01',
        'service_date' => '2025-01-01',
        'useful_life' => 3,
        'amortization_method' => 'linear',
        'status' => 'active',
        'provider_id' => $this->provider->id,
    ];

    $this->actingAs($user)
        ->postJson('/api/v1/fixed-assets/assets', $payload)
        ->assertStatus(201)
        ->assertJsonPath('reference', 'IMM-PEST-001');

    $this->assertDatabaseHas('assets', ['reference' => 'IMM-PEST-001']);
});

it('valide la cohérence chronologique des dates via FormRequest', function () {
    $category = AssetCategory::factory()->create();
    $user = User::factory()->create();

    // Date de mise en service (Janvier) AVANT acquisition (Février)
    $payload = [
        'asset_category_id' => $category->id,
        'acquisition_date' => '2025-02-01',
        'service_date' => '2025-01-01',
    ];

    $this->actingAs($user)
        ->postJson('/api/v1/fixed-assets/assets', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['service_date']);
});

it('interdit la suppression d\'un actif dont les écritures sont validées', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create();

    // On simule une écriture comptable validée
    \App\Models\AmortizationLine::factory()->create([
        'asset_id' => $asset->id,
        'is_posted' => true,
    ]);

    $this->actingAs($user)
        ->deleteJson("/api/v1/fixed-assets/assets/{$asset->id}")
        ->assertStatus(403)
        ->assertJson(['message' => 'Impossible de supprimer un actif comptabilisé.']);

    $this->assertNotSoftDeleted($asset);
});
