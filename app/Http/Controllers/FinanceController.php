<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeasingRequest;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Leasing;
use App\Models\Loan;
use Illuminate\Http\JsonResponse;

class FinanceController extends Controller
{
    public function storeLeasing(StoreLeasingRequest $request): JsonResponse
    {
        return response()->json(Leasing::create($request->validated()), 201);
    }

    public function storeLoan(StoreLoanRequest $request): JsonResponse
    {
        return response()->json(Loan::create($request->validated()), 201);
    }
}
