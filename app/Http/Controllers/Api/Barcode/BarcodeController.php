<?php

namespace App\Http\Controllers\Api\Barcode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Picqer\Barcode\Renderers\PngRenderer;
use Picqer\Barcode\Types\TypeCode128;

class BarcodeController extends Controller
{
    public function generateBarcode(Request $request)
    {
        $colorBlack = [0, 0, 0]; // Black foreground
        $colorWhite = [255, 255, 255]; // White background

        $barcodeValue = $request->barcode_value;

        // Generate Barcode
        $barcode = (new TypeCode128())->getBarcode($barcodeValue);
        $renderer = new PngRenderer();
        $renderer->setForegroundColor($colorBlack);
        $renderer->setBackgroundColor($colorWhite); // Set white background

        // Ensure directory exists
        $barcodeDirectory = storage_path('app/public/barcodes');
        if (!file_exists($barcodeDirectory)) {
            mkdir($barcodeDirectory, 0775, true);
        }

        // Save barcode file with unique name
        $fileName = 'barcode_' . time() . '.png';
        $filePath = $barcodeDirectory . '/' . $fileName;
        file_put_contents($filePath, $renderer->render($barcode, $barcode->getWidth() * 3, 50));

        // Return the public URL
        return response()->json([
            'statusCode' => 200,
            'message' => 'Barcode generated successfully',
            'barcode_url' => asset('storage/barcodes/' . $fileName),
        ], 200);
    }
}
