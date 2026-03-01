<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { size: A4; margin: 0; }
        body { margin: 0; padding: 0; background: white; }

        /* Grille d'étiquettes précise (3x8) */
        .label-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(8, 1fr);
            width: 210mm;
            height: 297mm;
            padding: 5mm;
            gap: 2mm;
        }

        .label-item {
            border: 1px dashed #e5e7eb;
            padding: 4mm;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            background: white;
        }
    </style>
</head>
<body>
<div class="label-grid">
    @foreach($assets as $asset)
        <div class="label-item">
            <div class="flex flex-col justify-center h-full w-2/3 pr-2">
                <p class="text-[8px] font-black text-blue-600 uppercase tracking-tighter mb-1">
                    {{ $settings->company_name ?? 'Immobilisator' }}
                </p>
                <p class="text-[10px] font-bold leading-none truncate mb-1">
                    {{ $asset['designation'] }}
                </p>
                <p class="text-[9px] font-mono bg-gray-100 inline-block px-1 self-start">
                    {{ $asset['reference'] }}
                </p>
            </div>
            <div class="w-1/3 flex justify-end">
                <img src="{{ $asset['qr_code'] }}" class="w-24 h-24">
            </div>
        </div>
    @endforeach
</div>
</body>
</html>
