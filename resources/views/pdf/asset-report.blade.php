<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { size: A4; margin: 0; }
        body { -webkit-print-color-adjust: exact; }
    </style>
</head>
<body class="bg-white p-10 font-sans text-gray-900">
<!-- Header -->
<div class="flex justify-between items-start border-b-4 border-blue-600 pb-6 mb-8">
    <div>
        <h1 class="text-3xl font-black text-blue-800 uppercase italic">Fiche Immobilisation</h1>
        <p class="text-lg font-bold text-gray-700 mt-1">{{ $asset->reference }}</p>
        <p class="text-md text-gray-500">{{ $asset->designation }}</p>
    </div>
    <div class="text-right">
        <h2 class="font-bold uppercase">{{ $settings->company_name ?? 'Ma Société' }}</h2>
        <p class="text-xs text-gray-500 italic">Édité le {{ now()->format('d/m/Y') }}</p>
    </div>
</div>

<div class="grid grid-cols-2 gap-8 mb-8">
    <!-- Bloc Identité -->
    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
        <h3 class="text-blue-700 font-bold uppercase text-xs tracking-widest mb-4 border-b pb-2">Identification</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Catégorie :</span>
                <span class="font-medium">{{ $asset->category?->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Localisation :</span>
                <span class="font-medium">{{ $asset->location?->name ?? 'Non localisé' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Fournisseur :</span>
                <span class="font-medium text-right">{{ $asset->provider?->name ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between border-t pt-2 mt-2">
                <span class="text-gray-500 font-bold">Statut actuel :</span>
                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-bold uppercase tracking-tighter">
                        {{ $asset->status->value }}
                    </span>
            </div>
        </div>
    </div>

    <!-- Bloc Financier -->
    <div class="bg-blue-50 p-5 rounded-xl border border-blue-100">
        <h3 class="text-blue-700 font-bold uppercase text-xs tracking-widest mb-4 border-b border-blue-200 pb-2">Données Comptables</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Valeur d'acquisition (HT) :</span>
                <span class="font-bold">{{ number_format($asset->acquisition_value, 2, ',', ' ') }} €</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Base amortissable :</span>
                <span class="font-bold">{{ number_format($asset->depreciable_basis, 2, ',', ' ') }} €</span>
            </div>
            <div class="flex justify-between border-t border-blue-200 pt-2 mt-2">
                <span class="text-gray-500">Mise en service :</span>
                <span class="font-medium">{{ $asset->service_date->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Durée / Méthode :</span>
                <span class="font-medium uppercase">{{ $asset->useful_life }} ans / {{ $asset->amortization_method->value }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Tableau d'amortissement -->
<div class="mb-8">
    <h3 class="text-gray-800 font-bold uppercase text-xs tracking-widest mb-4 flex items-center">
        Plan d'amortissement prévisionnel
    </h3>
    <table class="w-full text-xs">
        <thead>
        <tr class="bg-gray-800 text-white uppercase tracking-tighter">
            <th class="p-2 text-left rounded-tl-lg">Année</th>
            <th class="p-2 text-right">Base de calcul</th>
            <th class="p-2 text-right">Annuité</th>
            <th class="p-2 text-right">Cumul Amort.</th>
            <th class="p-2 text-right rounded-tr-lg">VNC</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @foreach($asset->amortizationLines as $line)
            <tr class="{{ $line->is_posted ? 'bg-green-50' : '' }}">
                <td class="p-2 font-bold">{{ $line->year }}</td>
                <td class="p-2 text-right">{{ number_format($line->base_value, 2, ',', ' ') }} €</td>
                <td class="p-2 text-right">{{ number_format($line->annuity_amount, 2, ',', ' ') }} €</td>
                <td class="p-2 text-right text-gray-500">{{ number_format($line->accumulated_amount, 2, ',', ' ') }} €</td>
                <td class="p-2 text-right font-black text-blue-900">{{ number_format($line->book_value, 2, ',', ' ') }} €</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if($asset->amortizationLines->where('is_posted', true)->count() > 0)
        <p class="text-[9px] text-gray-400 mt-2 italic">* Les lignes en vert sont déjà validées et clôturées.</p>
    @endif
</div>

<!-- Section Financement / Maintenance (Si existant) -->
@if($asset->interventions->count() > 0)
    <div class="mt-8">
        <h3 class="text-gray-800 font-bold uppercase text-xs tracking-widest mb-3">Historique des interventions</h3>
        <div class="grid grid-cols-1 gap-2">
            @foreach($asset->interventions as $intervention)
                <div class="flex justify-between text-[10px] p-2 bg-gray-50 border-l-4 border-blue-400">
                    <span>{{ $intervention->intervention_date->format('d/m/Y') }} - <span class="font-bold">{{ $intervention->title }}</span></span>
                    <span class="font-bold">{{ number_format($intervention->cost, 2, ',', ' ') }} € {{ $intervention->is_capitalized ? '(Capitalisé)' : '' }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Footer -->
<div class="fixed bottom-10 left-10 right-10 pt-4 border-t border-gray-100 text-[9px] text-gray-400 flex justify-between">
    <span>Généré par <strong class="text-blue-500">Immobilisator v1.0</strong></span>
    <span>Rapport de conformité comptable interne</span>
</div>
</body>
</html>
