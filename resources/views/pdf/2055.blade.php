<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { size: A4 landscape; margin: 0; }
        body { -webkit-print-color-adjust: exact; }
    </style>
</head>
<body class="bg-white p-8 font-sans text-gray-900">
<!-- Header -->
<div class="flex justify-between border-b-2 border-black pb-4 mb-6">
    <div>
        <h2 class="text-xl font-bold uppercase">{{ $settings->company_name ?? 'Ma Société' }}</h2>
        <p class="text-sm text-gray-600">SIRET : {{ $settings->siret ?? 'N/A' }}</p>
        <p class="text-sm text-gray-600 font-medium italic">Devise : {{ $settings->currency ?? 'EUR' }}</p>
    </div>
    <div class="text-right">
        <span class="inline-block border-2 border-black px-3 py-1 font-bold text-sm mb-2">FORMULAIRE 2055-SD</span>
        <p class="text-xs text-gray-500 uppercase">Direction Générale des Finances Publiques</p>
        <p class="text-sm font-bold">Exercice clos le 31/12/{{ $year }}</p>
    </div>
</div>

<!-- Title -->
<h1 class="text-2xl font-black uppercase text-center my-8 tracking-widest border-y py-2 bg-gray-50">
    Tableau des Amortissements
</h1>

<!-- Table -->
<table class="w-full border-collapse">
    <thead>
    <tr class="bg-gray-100 uppercase text-[10px] tracking-tighter leading-tight">
        <th class="border border-gray-300 p-2 text-left w-1/3">Éléments amortissables</th>
        <th class="border border-gray-300 p-2 text-right">Amortissements début exercice</th>
        <th class="border border-gray-300 p-2 text-right">Dotations de l'exercice</th>
        <th class="border border-gray-300 p-2 text-right text-red-700">Diminutions (Reprises sur sorties)</th>
        <th class="border border-gray-300 p-2 text-right bg-green-50">Amortissements fin exercice</th>
    </tr>
    </thead>
    <tbody class="text-[11px]">
    @foreach($assets as $asset)
        <tr class="hover:bg-gray-50">
            <td class="border border-gray-200 p-2">
                <div class="font-bold text-gray-800">{{ $asset['reference'] }}</div>
                <div class="text-gray-600 lowercase first-letter:uppercase">{{ $asset['designation'] }}</div>
                <div class="text-[9px] text-green-600 italic uppercase">{{ $asset['category'] }}</div>
            </td>
            <td class="border border-gray-200 p-2 text-right">{{ number_format($asset['amort_debut'], 2, ',', ' ') }}</td>
            <td class="border border-gray-200 p-2 text-right text-blue-600">+ {{ number_format($asset['dotations'], 2, ',', ' ') }}</td>
            <td class="border border-gray-200 p-2 text-right text-red-600">- {{ number_format($asset['reprises'], 2, ',', ' ') }}</td>
            <td class="border border-gray-200 p-2 text-right font-bold bg-green-50/30">
                {{ number_format($asset['amort_fin'], 2, ',', ' ') }}
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr class="bg-gray-800 text-white font-bold text-[12px]">
        <td class="p-3 text-right uppercase">Totaux des Amortissements</td>
        <td class="p-3 text-right border-l border-gray-600 italic font-normal text-gray-300">
            {{ number_format($assets->sum('amort_debut'), 2, ',', ' ') }}
        </td>
        <td class="p-3 text-right border-l border-gray-600">
            {{ number_format($assets->sum('dotations'), 2, ',', ' ') }}
        </td>
        <td class="p-3 text-right border-l border-gray-600">
            {{ number_format($assets->sum('reprises'), 2, ',', ' ') }}
        </td>
        <td class="p-3 text-right border-l border-gray-600 bg-green-900 text-yellow-400 text-sm">
            {{ number_format($assets->sum('amort_fin'), 2, ',', ' ') }}
        </td>
    </tr>
    </tfoot>
</table>

<!-- Footer -->
<div class="mt-12 pt-4 border-t border-gray-200 flex justify-between items-center text-[9px] text-gray-400">
    <div>Généré par <span class="font-bold italic text-blue-500 underline">IMMOBILISATOR</span> le {{ now()->format('d/m/Y H:i') }}</div>
    <div>{{ $settings->company_name }} - Page 1 / 1</div>
</div>
</body>
</html>
