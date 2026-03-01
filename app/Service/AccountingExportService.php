<?php

namespace App\Service;

use App\Models\AmortizationLine;

class AccountingExportService
{
    /**
     * Génère un fichier CSV des dotations pour une année donnée.
     * Format standard : Date, Journal, Compte, Libellé, Débit, Crédit
     */
    public function generateDotationsCsv(int $year): string
    {
        $lines = AmortizationLine::query()
            ->with(['asset.category'])
            ->where('year', $year)
            ->where('is_posted', false)
            ->get();

        $filename = "export_compta_dotations_{$year}_".now()->format('Ymd_His').'.csv';
        $handle = fopen('php://temp', 'r+');

        // En-têtes du CSV (Format type FEC / Standard comptable)
        fputcsv($handle, [
            'Date',
            'Journal',
            'Compte',
            'Référence',
            'Libellé',
            'Débit',
            'Crédit',
        ], ';');

        foreach ($lines as $line) {
            $asset = $line->asset;
            $category = $asset->category;
            $date = now()->setYear($year)->endOfYear()->format('d/m/Y');

            // 1. Ligne de Débit (Compte 681 - Dotations)
            fputcsv($handle, [
                $date,
                'OD', // Journal d'Opérations Diverses
                '681100', // Compte de charge par défaut
                $asset->reference,
                'DAP '.$year.' - '.$asset->designation,
                round($line->annuity_amount, 2),
                0,
            ], ';');

            // 2. Ligne de Crédit (Compte 28 - Amortissements)
            fputcsv($handle, [
                $date,
                'OD',
                $category->accounting_code_depreciation ?? '280000',
                $asset->reference,
                'DAP '.$year.' - '.$asset->designation,
                0,
                round($line->annuity_amount, 2),
            ], ';');
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return $csvContent;
    }
}
