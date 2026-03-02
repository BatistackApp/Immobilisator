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

<!-- Header avec QR Code -->
<div class="flex justify-between items-start border-b-4 border-blue-600 pb-6 mb-8">
    <div class="w-2/3">
        <h1 class="text-3xl font-black text-blue-800 uppercase italic">Fiche d'Intervention</h1>
        <p class="text-lg font-bold text-gray-700 mt-1">Actif : {{ $intervention->asset->reference }}</p>
        <p class="text-md text-gray-500">{{ $intervention->asset->designation }}</p>
    </div>
    <div class="w-1/3 flex flex-col items-end">
        @if(isset($qr_code_uri))
            <img src="{{ $qr_code_uri }}" class="w-24 h-24 mb-2 border p-1 bg-white">
        @endif
        <div class="text-right">
            <h2 class="font-bold uppercase text-sm">{{ $settings->company_name ?? 'Ma Société' }}</h2>
            <p class="text-[10px] text-gray-400">ID Intervention : #{{ $intervention->id }}</p>
        </div>
    </div>
</div>

<!-- Grille de données principales -->
<div class="grid grid-cols-2 gap-6 mb-8">
    <!-- Bloc Détails de l'intervention -->
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h3 class="text-blue-700 font-bold uppercase text-[10px] tracking-widest mb-3 border-b pb-1">Détails de l'intervention</h3>
        <div class="space-y-1.5 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Date :</span><span class="font-medium">{{ $intervention->intervention_date->format('d/m/Y H:i') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Type :</span><span class="font-medium">{{ $intervention->type->getLabel() }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Intervenant :</span><span class="font-medium">{{ $intervention->provider?->name ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Capitalisée :</span><span class="font-medium">{{ $intervention->is_capitalized ? 'Oui' : 'Non' }}</span></div>
            <div class="flex justify-between pt-2 mt-2 border-t font-bold text-base">
                <span class="text-gray-500">Coût :</span>
                <span class="text-red-600">{{ number_format($intervention->cost, 2, ',', ' ') }} €</span>
            </div>
        </div>
    </div>

    <!-- Bloc Identification de l'actif -->
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h3 class="text-blue-700 font-bold uppercase text-[10px] tracking-widest mb-3 border-b pb-1">Actif Concerné</h3>
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between"><span class="text-gray-500">Référence :</span><span class="font-medium">{{ $intervention->asset->reference }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Catégorie :</span><span class="font-medium">{{ $intervention->asset->category?->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Localisation :</span><span class="font-medium">{{ $intervention->asset->location?->name ?? 'N/A' }}</span></div>
            <div class="flex justify-between pt-1 font-bold">
                <span class="text-gray-500">Statut Actuel :</span>
                <span class="uppercase {{ $intervention->asset->status->value === 'active' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $intervention->asset->status->getLabel() }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Description -->
<div class="mb-8">
    <h3 class="text-gray-800 font-bold uppercase text-[10px] tracking-widest mb-2">Libellé & Description des travaux</h3>
    <div class="p-4 rounded-lg bg-white border border-gray-200 text-sm">
        <p class="font-bold text-gray-800">{{ $intervention->title }}</p>
        @if($intervention->description)
            <p class="text-gray-600 mt-2 text-xs">{{ $intervention->description }}</p>
        @endif
    </div>
</div>

<!-- Impact Comptable -->
@if($intervention->is_capitalized)
    <div class="mb-6 p-3 border-2 border-dashed border-blue-200 rounded-lg bg-blue-50">
        <h4 class="text-[10px] font-black uppercase text-blue-600 mb-2">Impact Comptable (Capitalisation)</h4>
        <p class="text-xs text-blue-800">
            Cette intervention a été capitalisée. Son coût de <strong>{{ number_format($intervention->cost, 2, ',', ' ') }} €</strong> a été ajouté à la valeur brute de l'actif.
            Le plan d'amortissement a été recalculé en conséquence à partir de la date de l'intervention.
        </p>
    </div>
@endif


<!-- Footer -->
<div class="fixed bottom-8 left-10 right-10 pt-4 border-t border-gray-100 text-[8px] text-gray-400 flex justify-between items-center">
    <div>Généré par <strong>Immobilisator</strong> le {{ now()->format('d/m/Y H:i') }}</div>
    <div class="uppercase tracking-tighter italic">Document de travail non contractuel</div>
    <div>Page 1 / 1</div>
</div>
</body>
</html>
