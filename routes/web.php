<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return redirect()->route('products.index');
});

// Product CRUD
Route::resource('products', ProductController::class);

// Barcode Image
Route::get('/product/{id}/barcode-image', [ProductController::class, 'barcodeImage'])
    ->name('product.barcode.image');

// Download Barcode
Route::get('/product/{id}/download-barcode', [ProductController::class, 'downloadBarcode'])
    ->name('product.barcode.download');

// Demo Barcode
Route::get('/generate-barcode', [ProductController::class, 'generateBarcode'])
    ->name('barcode.generate');

Route::get('/products-export', [ProductController::class, 'exportCsv'])
    ->name('products.export');