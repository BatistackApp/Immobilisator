<?php

namespace App\Service;

use App\Models\Asset;
use App\Models\CompanySettings;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

class AssetLabelService
{
    /**
     * Génère un PDF d'étiquettes pour une collection d'immobilisations.
     */
    public function generateLabelsPdf(Collection $assets): string
    {
        $settings = CompanySettings::first();

        // Configuration du QR Code
        $options = new QROptions([
            'version' => 5,
            'outputType' => QROutputInterface::GDIMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 5,
            'imageBase64' => true,
            'bgColor' => [255, 255, 255],
            'imageTransparent' => false,
        ]);

        $qrcode = new QRCode($options);

        // On prépare les données
        $preparedAssets = $assets->map(function (Asset $asset) use ($qrcode) {
            $url = url('/admin/assets/'.$asset->id);

            return [
                'reference' => $asset->reference,
                'designation' => $asset->designation,
                // generate() renvoie directement la string base64 avec les options choisies
                'qr_code' => $qrcode->render($url),
            ];
        });

        $html = View::make('pdf.asset-labels', [
            'assets' => $preparedAssets,
            'settings' => $settings,
        ])->render();

        // Rendu PDF via Browsershot
        return Browsershot::html($html)
            ->setNodeBinary(config('browsershot.node_binary_path'))
            ->setNpmBinary(config('browsershot.npm_binary_path'))
            ->format('A4')
            ->showBackground()
            ->noSandbox()
            ->setOption('args', ['--disable-web-security'])
            ->margins(0, 0, 0, 0)
            ->waitUntilNetworkIdle()
            ->pdf();
    }
}
