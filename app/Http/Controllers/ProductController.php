<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BarcodeLog;
use App\Models\ScanLog;
use Illuminate\Http\Request;
use DNS1D;
use DNS2D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // Display all products & Dashboard Stats
    public function index(Request $request)
    {
        $search = $request->search;

        $products = Product::search($search)
            ->oldest()
            ->paginate(10)
            ->withQueryString();

        $totalProducts = Product::count();
        $barcodeProducts = Product::whereNotNull('barcode')->where('barcode', '!=', '')->count();
        $withoutBarcode = Product::whereNull('barcode')->orWhere('barcode', '')->count();
        $todayProducts = Product::whereDate('created_at', today())->count();
        $totalStock = Product::sum('stock');

        return view('products.index', compact(
            'products',
            'search',
            'totalProducts',
            'barcodeProducts',
            'withoutBarcode',
            'todayProducts',
            'totalStock'
        ));
    }

    // Create form
    public function create()
    {
        return view('products.create');
    }

    // Store Product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'barcode_type' => 'nullable|string|in:C128,C39,EAN13,QRCODE',
            'description' => 'nullable|string',
        ]);

        $barcodeType = $request->input('barcode_type', 'C128');
        $productData = [
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'stock' => $request->stock ?? 10,
            'barcode_type' => $barcodeType,
            'description' => $request->description,
        ];

        if ($request->has('generate_barcode')) {
            $productData['barcode'] = $this->generateUniqueBarcode($request->sku);
        }

        $product = Product::create($productData);

        if ($request->has('generate_barcode')) {
            BarcodeLog::create([
                'product_id' => $product->id,
                'action' => 'generated',
                'ip_address' => request()->ip(),
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    // Show Product & Logs
    public function show($id)
    {
        $product = Product::with(['logs', 'scanLogs'])->findOrFail($id);
        return view('products.show', compact('product'));
    }

    // Edit form
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    // Update Product
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $id,
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'barcode_type' => 'nullable|string|in:C128,C39,EAN13,QRCODE',
            'description' => 'nullable|string',
        ]);

        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->stock = $request->stock ?? $product->stock;
        $product->barcode_type = $request->input('barcode_type', $product->barcode_type ?? 'C128');
        $product->description = $request->description;

        if ($request->has('regenerate_barcode')) {
            $product->barcode = $this->generateUniqueBarcode($request->sku);
            BarcodeLog::create([
                'product_id' => $product->id,
                'action' => 'regenerated',
                'ip_address' => request()->ip(),
            ]);
        }

        $product->save();

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    // Delete Product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }

    // Generate Barcode / QR Code Image Stream (Supports 1D & 2D QR Code)
    public function barcodeImage($id)
    {
        $product = Product::findOrFail($id);
        $code = $product->barcode ?? $product->sku;
        $type = $product->barcode_type ?? 'C128';

        if ($type === 'QRCODE') {
            $pngData = QrCode::format('png')->size(200)->margin(1)->generate($code);
            return response($pngData)->header('Content-Type', 'image/png');
        }

        $barcodePng = DNS1D::getBarcodePNG($code, $type === 'EAN13' ? 'EAN13' : 'C128', 2, 70);
        return response(base64_decode($barcodePng))->header('Content-Type', 'image/png');
    }

    // Download Barcode / QR Code PNG
    public function downloadBarcode($id)
    {
        $product = Product::findOrFail($id);

        BarcodeLog::create([
            'product_id' => $product->id,
            'action' => 'downloaded',
            'ip_address' => request()->ip(),
        ]);

        $code = $product->barcode ?? $product->sku;
        $type = $product->barcode_type ?? 'C128';

        if ($type === 'QRCODE') {
            $pngData = QrCode::format('png')->size(300)->margin(2)->generate($code);
            return response($pngData)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="' . $product->sku . '-qrcode.png"');
        }

        $barcodePng = DNS1D::getBarcodePNG($code, 'C128', 3, 100);
        return response(base64_decode($barcodePng))
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $product->sku . '-barcode.png"');
    }

    // Demo Barcode Generator Endpoint
    public function generateBarcode(Request $request)
    {
        $code = $request->get('code', '1234567890');

        if ($request->has('product_id')) {
            return redirect()->route('products.show', $request->product_id);
        }

        return redirect()->back()->with('code', $code);
    }

    // Bulk Batch Barcode Zip Bundler
    public function downloadBulkZip()
    {
        $products = Product::whereNotNull('barcode')->where('barcode', '!=', '')->get();
        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'No products with barcodes available.');
        }

        $zipFileName = 'barcodes_archive_' . date('Y_m_d_His') . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($products as $product) {
                $code = $product->barcode;
                $type = $product->barcode_type ?? 'C128';

                if ($type === 'QRCODE') {
                    $imageData = QrCode::format('png')->size(250)->generate($code);
                } else {
                    $imageData = base64_decode(DNS1D::getBarcodePNG($code, 'C128', 2, 70));
                }

                $zip->addFromString($product->sku . '_' . Str::slug($product->name) . '.png', $imageData);
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    // Live Web Camera Scanner & POS Lookup Endpoint
    public function scanLookup(Request $request)
    {
        $scannedCode = trim($request->input('code', ''));
        if (!$scannedCode) {
            return response()->json(['success' => false, 'message' => 'No barcode provided.'], 422);
        }

        $product = Product::where('barcode', $scannedCode)
            ->orWhere('sku', $scannedCode)
            ->first();

        if (!$product) {
            ScanLog::create([
                'scanned_code' => $scannedCode,
                'device_ip' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
                'action_taken' => 'Unrecognized Code',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Product not found for scanned code: ' . $scannedCode
            ], 444);
        }

        ScanLog::create([
            'product_id' => $product->id,
            'scanned_code' => $scannedCode,
            'device_ip' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'action_taken' => 'Looked Up',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product found!',
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => number_format($product->price, 2),
                'stock' => $product->stock,
                'barcode' => $product->barcode,
                'barcode_image' => route('product.barcode.image', $product->id),
            ]
        ]);
    }

    // Instant POS Stock Adjustment (+1 / -1)
    public function adjustStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|integer',
        ]);

        $product = Product::findOrFail($request->product_id);
        $product->stock = max(0, $product->stock + (int)$request->amount);
        $product->save();

        ScanLog::create([
            'product_id' => $product->id,
            'scanned_code' => $product->barcode ?? $product->sku,
            'device_ip' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'action_taken' => 'Stock ' . ($request->amount > 0 ? '+' . $request->amount : $request->amount),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully!',
            'new_stock' => $product->stock
        ]);
    }

    // Printable A4 Sticker Label Sheet Layout Studio (24 labels/sheet)
    public function labelSheet(Request $request)
    {
        $products = Product::whereNotNull('barcode')->get();
        return view('products.labels', compact('products'));
    }

    // Scan Audit Trail Logs View
    public function scanLogs()
    {
        $scanLogs = ScanLog::with('product')->latest()->paginate(15);
        return view('products.scan_logs', compact('scanLogs'));
    }

    // Export Products CSV
    public function exportCsv()
    {
        $fileName = 'products-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Product Name', 'SKU', 'Price ($)', 'Stock', 'Barcode', 'Barcode Type', 'Created At']);

            foreach (Product::latest()->get() as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->price,
                    $product->stock,
                    $product->barcode,
                    $product->barcode_type,
                    $product->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // RESTful JSON API Endpoint
    public function jsonApi()
    {
        return response()->json([
            'status' => 'success',
            'data' => Product::latest()->get()
        ]);
    }

    private function generateUniqueBarcode($sku)
    {
        $barcode = 'BC-' . strtoupper(substr(md5($sku . time()), 0, 10));
        while (Product::where('barcode', $barcode)->exists()) {
            $barcode = 'BC-' . strtoupper(substr(md5($sku . time() . rand(1000, 9999)), 0, 10));
        }
        return $barcode;
    }
}
