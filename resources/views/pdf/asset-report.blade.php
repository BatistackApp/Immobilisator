<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { size: A4; margin: 0; }
        body { -webkit-print-color-adjust: exact; }
        .watermark {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 8rem; opacity: 0.05; font-weight: 900;
            pointer-events: none; width: 100%; text-align: center;
        }
    </style>
</head>
<body class="bg-white p-10 font-sans text-gray-900 relative">

@if($asset->status->value === 'disposed')
    <div class="watermark uppercase text-red-600">SORTI DU BILAN</div>
@endif

<!-- Header avec QR Code -->
<div class="flex justify-between items-start border-b-4 border-blue-600 pb-6 mb-8">
    <div class="w-2/3">
        <h1 class="text-3xl font-black text-blue-800 uppercase italic">Fiche Immobilisation</h1>
        <p class="text-lg font-bold text-gray-700 mt-1">{{ $asset->reference }}</p>
        <p class="text-md text-gray-500">{{ $asset->designation }}</p>
    </div>
    <div class="w-1/3 flex flex-col items-end">
        @if(isset($qr_code_uri))
            <img src="{{ $qr_code_uri }}" class="w-24 h-24 mb-2 border p-1 bg-white">
        @endif
        <div class="text-right">
            <h2 class="font-bold uppercase text-sm">{{ $settings->company_name ?? 'Ma Société' }}</h2>
            <p class="text-[10px] text-gray-400">ID Interne : #{{ $asset->id }}</p>
        </div>
    </div>
</div>

<!-- Grille de données principales -->
<div class="grid grid-cols-2 gap-6 mb-8">
    <!-- Bloc Identité & Localisation -->
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h3 class="text-blue-700 font-bold uppercase text-[10px] tracking-widest mb-3 border-b pb-1">Identification</h3>
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between"><span class="text-gray-500">Catégorie :</span><span class="font-medium">{{ $asset->category?->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Localisation :</span><span class="font-medium">{{ $asset->location?->name ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Fournisseur :</span><span class="font-medium">{{ $asset->provider?->name ?? 'N/A' }}</span></div>
            <div class="flex justify-between pt-1 font-bold">
                <span class="text-gray-500">Statut :</span>
                <span class="uppercase {{ $asset->status->value === 'active' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $asset->status->value }}
                    </span>
            </div>
        </div>
    </div>

    <!-- Bloc Comptable & Fiscal -->
    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
        <h3 class="text-blue-700 font-bold uppercase text-[10px] tracking-widest mb-3 border-b border-blue-200 pb-1">Données Financières</h3>
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between"><span class="text-gray-500">Valeur d'Acquisition :</span><span class="font-bold">{{ number_format($asset->acquisition_value, 2, ',', ' ') }} €</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Base Amortissable :</span><span class="font-bold">{{ number_format($asset->depreciable_basis, 2, ',', ' ') }} €</span></div>
            @if($asset->revaluation_surplus > 0)
                <div class="flex justify-between text-orange-700 font-bold">
                    <span>Écart Réévaluation :</span>
                    <span>+ {{ number_format($asset->revaluation_surplus, 2, ',', ' ') }} €</span>
                </div>
            @endif
            <div class="flex justify-between border-t border-blue-200 pt-1 mt-1">
                <span class="text-gray-500">Mise en service :</span>
                <span class="font-medium">{{ $asset->service_date->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Méthode / Durée :</span>
                <span class="font-medium uppercase">{{ $asset->amortization_method->value }} / {{ $asset->useful_life }} ans</span>
            </div>
        </div>
    </div>
</div>

<!-- Détails Spécifiques (Leasing / Loan / Sortie) -->
@if($asset->funding_type->value === 'leasing' && $asset->leasing)
    <div class="mb-6 p-3 border-2 border-dashed border-gray-200 rounded-lg bg-gray-50">
        <h4 class="text-[10px] font-black uppercase text-gray-400 mb-2">Informations de Financement (Crédit-Bail)</h4>
        <div class="grid grid-cols-3 text-xs gap-4">
            <div><span class="text-gray-500 block">Bailleur</span> {{ $asset->leasing->provider->name }}</div>
            <div><span class="text-gray-500 block">Loyer Mensuel</span> {{ number_format($asset->leasing->monthly_rent, 2, ',', ' ') }} €</div>
            <div><span class="text-gray-500 block">Fin de contrat</span> {{ $asset->leasing->end_date->format('d/m/Y') }}</div>
        </div>
    </div>
@endif

{{-- AJOUT DU BLOC EMPRUNT --}}
@if($asset->funding_type->value === 'loan' && $asset->loan)
    <div class="mb-6 p-3 border-2 border-dashed border-blue-200 rounded-lg bg-blue-50/30">
        <h4 class="text-[10px] font-black uppercase text-blue-600 mb-2">Informations de Financement (Emprunt Bancaire)</h4>
        <div class="grid grid-cols-4 text-xs gap-4">
            <div><span class="text-gray-500 block">Banque</span> {{ $asset->loan->provider->name }}</div>
            <div><span class="text-gray-500 block">Montant Emprunté</span> {{ number_format($asset->loan->principal_amount, 2, ',', ' ') }} €</div>
            <div><span class="text-gray-500 block">Taux / Durée</span> {{ $asset->loan->interest_rate }}% / {{ $asset->loan->duration_months }} mois</div>
            <div><span class="text-gray-500 block">1ère Échéance</span> {{ $asset->loan->first_installment_date->format('d/m/Y') }}</div>
        </div>
    </div>
@endif

@if($asset->status->value === 'disposed' && isset($asset->metadata['disposal_date']))
    <div class="mb-6 p-3 border-2 border-red-200 rounded-lg bg-red-50">
        <h4 class="text-[10px] font-black uppercase text-red-600 mb-2">Détails de la Sortie d'Actif</h4>
        <div class="grid grid-cols-3 text-xs gap-4">
            <div><span class="text-gray-500 block">Date de sortie</span> {{ \Carbon\Carbon::parse($asset->metadata['disposal_date'])->format('d/m/Y') }}</div>
            <div><span class="text-gray-500 block">Prix de Cession</span> {{ number_format($asset->metadata['selling_price'] ?? 0, 2, ',', ' ') }} €</div>
            <div><span class="text-gray-500 block">Résultat (± Value)</span>
                <span class="{{ ($asset->metadata['gain_loss'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} font-bold">
                        {{ number_format($asset->metadata['gain_loss'] ?? 0, 2, ',', ' ') }} €
                    </span>
            </div>
        </div>
    </div>
@endif

<!-- Tableau d'Amortissement -->
<div class="mb-6">
    <h3 class="text-gray-800 font-bold uppercase text-[10px] tracking-widest mb-3">Plan d'Amortissement Comptable</h3>
    <table class="w-full text-[10px] border-collapse">
        <thead>
        <tr class="bg-gray-800 text-white uppercase">
            <th class="p-1.5 text-left">Année</th>
            <th class="p-1.5 text-right">Base</th>
            <th class="p-1.5 text-right">Annuité</th>
            <th class="p-1.5 text-right">Cumul</th>
            <th class="p-1.5 text-right">VNC</th>
            <th class="p-1.5 text-center">État</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @foreach($asset->amortizationLines as $line)
            <tr>
                <td class="p-1.5 font-bold">{{ $line->year }}</td>
                <td class="p-1.5 text-right">{{ number_format($line->base_value, 2, ',', ' ') }} €</td>
                <td class="p-1.5 text-right font-bold">{{ number_format($line->annuity_amount, 2, ',', ' ') }} €</td>
                <td class="p-1.5 text-right text-gray-500">{{ number_format($line->accumulated_amount, 2, ',', ' ') }} €</td>
                <td class="p-1.5 text-right font-black text-blue-900">{{ number_format($line->book_value, 2, ',', ' ') }} €</td>
                <td class="p-1.5 text-center">
                    @if($line->is_posted)
                        <span class="text-green-600 font-bold">POSTÉ</span>
                    @else
                        <span class="text-gray-300 italic">PRÉV.</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@if($asset->interventions->count() > 0)
    <div class="mb-6">
        <h3 class="text-gray-800 font-bold uppercase text-[10px] tracking-widest mb-3">Historique des Interventions</h3>
        <table class="w-full text-[10px] border-collapse">
            <thead>
            <tr class="bg-gray-100 text-gray-700 uppercase">
                <th class="p-1.5 text-left">Date</th>
                <th class="p-1.5 text-right">Intervenant</th>
                <th class="p-1.5 text-right">Type</th>
                <th class="p-1.5 text-right">Désignation</th>
                <th class="p-1.5 text-right">Montant</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @foreach($asset->interventions as $line)
                <tr>
                    <td class="p-1.5 font-bold">{{ $line->intervention_date->format("d/m/Y") }}</td>
                    <td class="p-1.5 text-right">{{ $line->provider->name }}</td>
                    <td class="p-1.5 text-right font-bold">{{ $line->type->getLabel() }}</td>
                    <td class="p-1.5 text-right">
                        <span class="font-bold">{{ $line->title }}</span><br>
                        <span class="text-gray-600">{{ $line->description }}</span>
                    </td>
                    <td class="p-1.5 text-right font-bold text-red-600">{{ number_format($line->cost, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

<!-- Footer -->
<div class="fixed bottom-8 left-10 right-10 pt-4 border-t border-gray-100 text-[8px] text-gray-400 flex justify-between items-center">
    <div>Généré par <strong>Immobilisator</strong> le {{ now()->format('d/m/Y H:i') }}</div>
    <div class="uppercase tracking-tighter italic">Document de travail confidentiel</div>
    <div>{{ $asset->reference }} - Page 1 / 1</div>
</div>
</body>
</html>
