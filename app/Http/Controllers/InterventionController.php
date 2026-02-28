<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterventionRequest;
use App\Models\Asset;
use App\Models\Intervention;
use Illuminate\Http\JsonResponse;

class InterventionController extends Controller
{
    public function store(StoreInterventionRequest $request): JsonResponse
    {
        $intervention = Intervention::create($request->validated());

        return response()->json($intervention, 201);
    }

    public function index(Asset $asset): JsonResponse
    {
        return response()->json($asset->interventions);
    }
}
