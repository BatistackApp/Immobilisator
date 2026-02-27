<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProviderRequest;
use App\Models\Provider;
use Illuminate\Http\JsonResponse;

class ProviderController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Provider::all());
    }

    public function store(StoreProviderRequest $request): JsonResponse
    {
        return response()->json(Provider::create($request->validated()), 201);
    }

    public function show(Provider $provider): JsonResponse
    {
        return response()->json($provider->load('assets'));
    }
}
