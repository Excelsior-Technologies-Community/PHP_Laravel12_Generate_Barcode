<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return redirect()->route('products.index');
});

// Product CRUD
Route::resource('products', ProductController::class);

// Barcode & QR Code Stream / Download
Route::get('/product/{id}/barcode-image', [ProductController::class, 'barcodeImage'])->name('product.barcode.image');
Route::get('/product/{id}/download-barcode', [ProductController::class, 'downloadBarcode'])->name('product.barcode.download');

// Demo Barcode Generator
Route::get('/generate-barcode', [ProductController::class, 'generateBarcode'])->name('barcode.generate');

// Bulk Batch Barcode Zip Export
Route::get('/bulk/download-zip', [ProductController::class, 'downloadBulkZip'])->name('products.bulk.zip');

// Live Web Camera Barcode / QR Scanner & Stock Adjustment
Route::post('/scan/lookup', [ProductController::class, 'scanLookup'])->name('scan.lookup');
Route::post('/scan/stock', [ProductController::class, 'adjustStock'])->name('scan.stock');
Route::get('/scan/logs', [ProductController::class, 'scanLogs'])->name('scan.logs');

// Printable A4 Sticker Label Sheet Studio
Route::get('/labels/print-sheet', [ProductController::class, 'labelSheet'])->name('labels.print');

// Exporters & REST API
Route::get('/products-export', [ProductController::class, 'exportCsv'])->name('products.export');
Route::get('/api/products', [ProductController::class, 'jsonApi'])->name('api.products');