<?php

namespace App\Http\Controllers;

use App\Service\AccountingIntegrationService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function journal(int $year, AccountingIntegrationService $service): JsonResponse
    {
        return response()->json($service->generateJournalEntries($year));
    }
}
