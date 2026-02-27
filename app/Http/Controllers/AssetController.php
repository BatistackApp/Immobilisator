<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Models\Asset;
use App\Service\AmortizationService;
use App\Service\DisposalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AssetController extends Controller
{
    public function __construct(
        protected AmortizationService $amortizationService,
        protected DisposalService $disposalService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(Asset::with(['category', 'location'])->paginate(15));
    }

    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = Asset::create($request->validated());
        return response()->json($asset, 201);
    }

    public function show(Asset $asset): JsonResponse
    {
        return response()->json($asset->load(['category', 'location', 'provider', 'amortizationLines', 'interventions', 'leasing', 'loan']));
    }

    public function update(StoreAssetRequest $request, Asset $asset): JsonResponse
    {
        $asset->update($request->validated());
        return response()->json($asset);
    }

    public function destroy(Asset $asset): JsonResponse
    {
        if ($asset->amortizationLines()->where('is_posted', true)->exists()) {
            return response()->json(['message' => 'Impossible de supprimer un actif comptabilisé.'], 403);
        }
        $asset->delete();
        return response()->json(null, 204);
    }

    /**
     * Action : Recalculer manuellement le plan d'amortissement
     */
    public function recalculate(Asset $asset): JsonResponse
    {
        $this->amortizationService->generateSchedule($asset);
        return response()->json(['message' => 'Plan d\'amortissement mis à jour.', 'lines' => $asset->amortizationLines]);
    }

    /**
     * Action : Sortie d'actif (Cession / Mise au rebut)
     */
    public function dispose(Request $request, Asset $asset): JsonResponse
    {
        $request->validate([
            'disposal_date' => 'required|date',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        $result = $this->disposalService->processDisposal(
            $asset,
            Carbon::parse($request->disposal_date),
            $request->selling_price ?? 0
        );

        return response()->json([
            'message' => 'L\'actif a été cédé avec succès.',
            'summary' => $result,
        ]);
    }
}
