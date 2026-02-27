<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ProviderController;
use App\Http\Requests\StoreInterventionRequest;
use App\Models\Intervention;
use App\Service\AccountingIntegrationService;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/fixed-assets')->group(function () {

    // Immobilisations
    Route::apiResource('assets', AssetController::class);
    Route::post('assets/{asset}/recalculate', [AssetController::class, 'recalculate']);
    Route::post('assets/{asset}/dispose', [AssetController::class, 'dispose']);

    // Tiers
    Route::apiResource('providers', ProviderController::class);

    // Interventions
    Route::post('interventions', function (StoreInterventionRequest $request) {
        return response()->json(Intervention::create($request->validated()), 201);
    });

    // Financements
    Route::post('leasings', [FinanceController::class, 'storeLeasing']);
    Route::post('loans', [FinanceController::class, 'storeLoan']);

    // Comptabilité
    Route::get('accounting/journal/{year}', function ($year, AccountingIntegrationService $service) {
        return response()->json($service->generateJournalEntries((int) $year));
    });
});
