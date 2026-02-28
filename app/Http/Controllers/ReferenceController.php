<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Models\Location;
use Illuminate\Http\JsonResponse;

class ReferenceController extends Controller
{
    public function categories(): JsonResponse
    {
        return response()->json(AssetCategory::all());
    }

    public function locations(): JsonResponse
    {
        return response()->json(Location::all());
    }
}
