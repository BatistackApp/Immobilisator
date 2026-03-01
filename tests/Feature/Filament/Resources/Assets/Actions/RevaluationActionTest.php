<?php

use App\Models\Asset;
use App\Models\User;
use App\Service\RevaluationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->actingAs($this->admin);
});

it('affiche le formulaire de réévaluation avec les bonnes données', function () {
    $asset = Asset::factory()->create();

    Livewire::test(\App\Filament\Resources\Assets\Pages\EditAsset::class, [
        'record' => $asset->getKey(),
    ])
        ->assertTableActionExists('revaluate') // Si l'action est dans la table
        ->assertActionExists('revaluate');      // Si l'action est dans le header ou EditPage
});

it('appelle le service de réévaluation lors de la soumission du formulaire', function () {
    $asset = Asset::factory()->create(['acquisition_value' => 5000]);

    // On s'assure que le service est bien appelé avec les données du formulaire
    $this->mock(RevaluationService::class, function (MockInterface $mock) use ($asset) {
        $mock->shouldReceive('revaluate')
            ->once()
            ->withArgs(function ($passedAsset, $date, $fairValue, $expert, $notes) use ($asset) {
                return $passedAsset->id === $asset->id &&
                    $fairValue === 8000.0 &&
                    $expert === 'Cabinet Expert-Comptable';
            });
    });

    Livewire::test(\App\Filament\Resources\Assets\Pages\EditAsset::class, [
        'record' => $asset->getKey(),
    ])
        ->mountAction('revaluate')
        ->setActionData([
            'revaluation_date' => now()->format('Y-m-d'),
            'fair_value' => 8000,
            'expert_name' => 'Cabinet Expert-Comptable',
            'notes' => 'Rapport annuel',
        ])
        ->callAction('revaluate')
        ->assertHasNoActionErrors()
        ->assertNotified('Réévaluation appliquée');
});

it('valide que la juste valeur est obligatoire', function () {
    $asset = Asset::factory()->create();

    Livewire::test(\App\Filament\Resources\Assets\Pages\EditAsset::class, [
        'record' => $asset->getKey(),
    ])
        ->mountAction('revaluate')
        ->setActionData(['fair_value' => null])
        ->callAction('revaluate')
        ->assertHasActionErrors(['fair_value' => 'required']);
});
