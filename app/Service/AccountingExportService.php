<?php

namespace App\Service;

use App\Models\AmortizationLine;
use DB;
use Filament\Notifications\Notification;

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

    public function comptabilisation(string $csvContent, string $currentYear): ?string
    {
        if (empty(trim($csvContent))) { // Vérifier si le CSV est vide (pas de lignes à exporter)
            Notification::make()
                ->title('Aucune donnée à exporter')
                ->body("Il n'y a aucune ligne d'amortissement non comptabilisée pour l'exercice $currentYear.")
                ->warning()
                ->send();

            return null; // Ne pas continuer si rien à exporter
        }

        DB::beginTransaction();

        try {
            $updatedCount = AmortizationLine::where('year', $currentYear)
                ->where('is_posted', false)
                ->update(['is_posted' => true]);

            DB::commit(); // Commit la transaction si tout se passe bien

            if ($updatedCount > 0) {
                Notification::make()
                    ->title('Exportation réussie et lignes verrouillées')
                    ->body("$updatedCount lignes d'amortissement ont été marquées comme comptabilisées. Le téléchargement va commencer.")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Aucune nouvelle donnée')
                    ->body("Toutes les lignes de l'exercice $currentYear ont déjà été comptabilisées.")
                    ->warning()
                    ->send();
            }

            // Déclenche le téléchargement après la mise à jour transactionnelle
            return $csvContent;

        } catch (\Exception $e) {
            DB::rollBack(); // Annule la transaction en cas d'erreur
            Notification::make()
                ->title('Erreur lors de l\'exportation')
                ->body("Une erreur est survenue lors de la mise à jour des lignes d'amortissement : ".$e->getMessage())
                ->danger()
                ->send();
            // Log l'erreur pour débogage
            \Log::error('Erreur lors de l\'exportation comptable', ['exception' => $e->getMessage(), 'year' => $currentYear]);

            return null;
        }
    }
}
