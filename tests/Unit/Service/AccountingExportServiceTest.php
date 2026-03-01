<?php

use App\Models\AmortizationLine;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\CostCenter;
use App\Service\AccountingExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new AccountingExportService;
});

it('génère un CSV vide avec uniquement les en-têtes si aucune ligne n\'existe', function () {
    $csv = $this->service->generateDotationsCsv(2025);

    $rows = explode("\n", trim($csv));
    expect($rows)->toHaveCount(1)
        ->and($rows[0])->toBe('Date;Journal;Compte;Référence;Libellé;Débit;Crédit');
});

it('génère correctement les lignes de débit et crédit dans le CSV', function () {
    $category = AssetCategory::factory()->create([
        'accounting_code_depreciation' => '281830',
    ]);

    $asset = Asset::factory()->create([
        'asset_category_id' => $category->id,
        'reference' => 'REF-001',
        'designation' => 'PC Portable',
    ]);

    AmortizationLine::create([
        'asset_id' => $asset->id,
        'year' => 2025,
        'base_value' => 1000,
        'annuity_amount' => 333.33,
        'accumulated_amount' => 333.33,
        'book_value' => 666.67,
        'is_posted' => false,
    ]);

    $csv = $this->service->generateDotationsCsv(2025);
    $rows = explode("\n", trim($csv));

    // En-tête + 1 ligne Débit + 1 ligne Crédit = 3 lignes
    expect($rows)->toHaveCount(3)
        ->and($rows[1])->toContain('31/12/2025;OD;681100;REF-001;DAP 2025 - PC Portable;333,33;0')
        ->and($rows[2])->toContain('31/12/2025;OD;281830;REF-001;DAP 2025 - PC Portable;0;333,33');
});

it('n\'inclut pas les lignes déjà comptabilisées (is_posted = true)', function () {
    $asset = Asset::factory()->create();

    AmortizationLine::create([
        'asset_id' => $asset->id,
        'year' => 2025,
        'base_value' => 1000,
        'annuity_amount' => 500,
        'accumulated_amount' => 500,
        'book_value' => 500,
        'is_posted' => true, // Déjà posté
    ]);

    $csv = $this->service->generateDotationsCsv(2025);
    $rows = explode("\n", trim($csv));

    expect($rows)->toHaveCount(1); // Uniquement l'en-tête
});

it('stoppe l\'opération et notifie si le contenu CSV est vide', function () {
    $result = $this->service->markLinesAsPostedAndNotify('', 2025);

    expect($result)->toBeNull();
    Notification::assertSentTo(
        auth()->user(),
        fn ($notification) => $notification->title === 'Aucune donnée à exporter'
    );
});

it('verrouille les lignes et notifie en cas de succès', function () {
    $asset = Asset::factory()->create();
    AmortizationLine::create([
        'asset_id' => $asset->id,
        'year' => 2025,
        'base_value' => 1000,
        'annuity_amount' => 500,
        'accumulated_amount' => 500,
        'book_value' => 500,
        'is_posted' => false,
    ]);

    $csvContent = 'data';
    $result = $this->service->markLinesAsPostedAndNotify($csvContent, 2025);

    expect($result)->toBe($csvContent)
        ->and(AmortizationLine::where('is_posted', true)->count())->toBe(1);

    Notification::assertSentTo(
        auth()->user(),
        fn ($notification) => $notification->title === 'Exportation réussie et lignes verrouillées'
    );
});

it('annule la transaction (rollback) et log l\'erreur en cas d\'exception', function () {
    Log::shouldReceive('error')->once();

    $asset = Asset::factory()->create();
    AmortizationLine::create([
        'asset_id' => $asset->id,
        'year' => 2025,
        'base_value' => 1000,
        'annuity_amount' => 500,
        'accumulated_amount' => 500,
        'book_value' => 500,
        'is_posted' => false,
    ]);

    // On force une erreur pendant l'update en utilisant un mock partiel ou en provoquant une erreur DB
    // Ici, on simule une erreur de transaction
    DB::shouldReceive('beginTransaction')->once();
    DB::shouldReceive('rollBack')->once();

    // On force l'exception sur l'update
    $mock = Mockery::mock('overload:App\Models\AmortizationLine');
    $mock->shouldReceive('where->where->update')->andThrow(new \Exception('Erreur critique'));

    $result = $this->service->markLinesAsPostedAndNotify('content', 2025);

    expect($result)->toBeNull();
    Notification::assertSentTo(
        auth()->user(),
        fn ($notification) => $notification->title === 'Erreur lors de l\'exportation'
    );
});

it('inclut la 8ème colonne Analytique dans l\'export CSV', function () {
    $service = new AccountingExportService;
    $csv = $service->generateDotationsCsv(2025);

    $header = explode(';', explode("\n", trim($csv))[0]);

    // On vérifie que la 8ème colonne est bien "Analytique"
    expect($header)->toHaveCount(8)
        ->and($header[7])->toBe('Analytique');
});

it('exporte le code analytique correct pour un actif affecté', function () {
    $costCenter = CostCenter::create(['code' => 'FACTORY_01', 'name' => 'Usine 1']);

    $asset = Asset::factory()->create([
        'cost_center_id' => $costCenter->id,
        'reference' => 'MAC-001',
    ]);

    AmortizationLine::create([
        'asset_id' => $asset->id,
        'year' => 2025,
        'base_value' => 1000,
        'annuity_amount' => 200,
        'accumulated_amount' => 200,
        'book_value' => 800,
        'is_posted' => false,
    ]);

    $service = new AccountingExportService;
    $csv = $service->generateDotationsCsv(2025);
    $rows = explode("\n", trim($csv));

    // On vérifie la ligne de débit (index 1)
    $cols = explode(';', $rows[1]);

    // La 8ème colonne (index 7) doit contenir le code FACTORY_01
    expect($cols[7])->toBe('FACTORY_01');
});

it('laisse la colonne analytique vide si aucun centre n\'est affecté', function () {
    $asset = Asset::factory()->create(['cost_center_id' => null]);

    AmortizationLine::create([
        'asset_id' => $asset->id,
        'year' => 2025,
        'base_value' => 1000,
        'annuity_amount' => 100,
        'accumulated_amount' => 100,
        'book_value' => 900,
        'is_posted' => false,
    ]);

    $service = new AccountingExportService;
    $csv = $service->generateDotationsCsv(2025);
    $rows = explode("\n", trim($csv));
    $cols = explode(';', $rows[1]);

    expect($cols[7])->toBe('');
});
