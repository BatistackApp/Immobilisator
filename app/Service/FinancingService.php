<?php

namespace App\Service;

use App\Models\Loan;

class FinancingService
{
    /**
     * Calcule les mensualités d'un emprunt (Annuités constantes).
     */
    public function calculateLoanInstallments(Loan $loan): array
    {
        $principal = $loan->principal_amount;
        $periodicRate = ($loan->interest_rate / 100) / 12;
        $nbPayments = $loan->duration_months;

        // Formule : M = P [ i(1 + i)^n ] / [ (1 + i)^n – 1 ]
        $monthlyPayment = $principal * ($periodicRate * pow(1 + $periodicRate, $nbPayments))
            / (pow(1 + $periodicRate, $nbPayments) - 1);

        $schedule = [];
        $remainingBalance = $principal;

        for ($i = 1; $i <= $nbPayments; $i++) {
            $interest = $remainingBalance * $periodicRate;
            $capital = $monthlyPayment - $interest;
            $remainingBalance -= $capital;

            $schedule[] = [
                'period' => $i,
                'payment' => round($monthlyPayment, 2),
                'interest' => round($interest, 2),
                'capital' => round($capital, 2),
                'remaining_balance' => max(0, round($remainingBalance, 2)),
            ];
        }

        return $schedule;
    }
}
