<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/fixed-assets')->group(function () {

    // Immobilisations principales
    Route::apiResource('assets', AssetController::class);
    Route::post('assets/{asset}/recalculate', [AssetController::class, 'recalculate']);
    Route::post('assets/{asset}/dispose', [AssetController::class, 'dispose']);

    // Tiers et Interventions
    Route::apiResource('providers', ProviderController::class);
    Route::post('interventions', [InterventionController::class, 'store']);
    Route::get('assets/{asset}/interventions', [InterventionController::class, 'index']);

    // Financements
    Route::post('leasings', [FinanceController::class, 'storeLeasing']);
    Route::post('loans', [FinanceController::class, 'storeLoan']);

    // Référentiels
    Route::get('categories', [ReferenceController::class, 'categories']);
    Route::get('locations', [ReferenceController::class, 'locations']);

    // Comptabilité
    Route::get('accounting/journal/{year}', [ReportController::class, 'journal']);
});
