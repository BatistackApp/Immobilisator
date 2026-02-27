<?php

namespace App\Service;

use App\Models\AmortizationLine;

class AccountingIntegrationService
{
    public function generateJournalEntries(int $fiscalYear): array
    {
        $lines = AmortizationLine::with('asset.category')
            ->where('year', $fiscalYear)
            ->where('is_posted', false)
            ->get();

        $entries = [];
        foreach ($lines as $line) {
            $entries[] = [
                'account' => '6811',
                'label' => "Dotation immo {$line->asset->reference}",
                'debit' => $line->annuity_amount,
                'credit' => 0,
            ];
            $entries[] = [
                'account' => $line->asset->category->accounting_code_depreciation,
                'label' => "Amortissement immo {$line->asset->reference}",
                'debit' => 0,
                'credit' => $line->annuity_amount,
            ];
        }

        return $entries;
    }
}
