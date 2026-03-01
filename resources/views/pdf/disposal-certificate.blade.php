<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { size: A4; margin: 2cm; }
        body { -webkit-print-color-adjust: exact; }
    </style>
</head>
<body class="bg-white font-sans text-gray-900">
<div class="border-4 border-double border-gray-200 p-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-12">
        <div>
            <h1 class="text-2xl font-black uppercase text-gray-800">Certificat de Sortie d'Actif</h1>
            <p class="text-sm text-gray-500 italic">Document comptable interne</p>
        </div>
        <div class="text-right text-xs">
            <p class="font-bold">{{ $settings->company_name }}</p>
            <p>{{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    <!-- Asset Identity -->
    <div class="mb-8">
        <h2 class="bg-gray-100 p-2 font-bold uppercase text-sm mb-4">Identification du bien</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <p><span class="text-gray-500">Référence :</span> {{ $asset->reference }}</p>
            <p><span class="text-gray-500">Désignation :</span> {{ $asset->designation }}</p>
            <p><span class="text-gray-500">Date d'acquisition :</span> {{ $asset->acquisition_date->format('d/m/Y') }}</p>
            <p><span class="text-gray-500">Valeur d'origine :</span> {{ number_format($asset->acquisition_value, 2, ',', ' ') }} €</p>
        </div>
    </div>

    <!-- Disposal Details -->
    <div class="mb-8">
        <h2 class="bg-red-50 p-2 font-bold uppercase text-sm text-red-800 mb-4 border-l-4 border-red-600">Détails de la sortie</h2>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span>Date de sortie effective :</span>
                <span class="font-bold">{{ \Carbon\Carbon::parse($asset->metadata['disposal_date'])->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Motif :</span>
                <span class="italic text-gray-700">{{ $asset->metadata['disposal_reason'] ?? 'Non spécifié' }}</span>
            </div>
            <div class="flex justify-between pt-2 border-t">
                <span>Prix de cession réalisé :</span>
                <span class="font-bold">{{ number_format($asset->metadata['selling_price'], 2, ',', ' ') }} €</span>
            </div>
            <div class="flex justify-between">
                <span>Valeur Nette Comptable (VNC) à la sortie :</span>
                <span class="font-bold text-gray-600">{{ number_format($asset->amortizationLines->last()->book_value, 2, ',', ' ') }} €</span>
            </div>
            <div class="flex justify-between text-lg border-t-2 border-black pt-2 mt-4">
                <span class="font-black uppercase">Résultat de cession :</span>
                <span class="font-black {{ $asset->metadata['gain_loss'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        {{ number_format($asset->metadata['gain_loss'], 2, ',', ' ') }} €
                    </span>
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="mt-24 grid grid-cols-2 gap-20">
        <div class="border-t border-gray-300 pt-4">
            <p class="text-[10px] uppercase font-bold text-gray-400 mb-12">Le Responsable Comptable</p>
            <p class="text-xs italic text-gray-300 italic">Signature et cachet</p>
        </div>
        <div class="border-t border-gray-300 pt-4 text-right">
            <p class="text-[10px] uppercase font-bold text-gray-400 mb-12">La Direction</p>
            <p class="text-xs italic text-gray-300 italic">Bon pour accord de sortie</p>
        </div>
    </div>
</div>
</body>
</html>
