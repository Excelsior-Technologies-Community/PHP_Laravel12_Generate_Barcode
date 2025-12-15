<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return redirect()->route('products.index');
});

// Product routes
Route::resource('products', ProductController::class);

// Additional routes for barcode
Route::get('/product/{id}/barcode-image', [ProductController::class, 'barcodeImage'])->name('product.barcode.image');

// Change this route to accept query parameter instead of URL parameter
Route::get('/generate-barcode', [ProductController::class, 'generateBarcode'])->name('barcode.generate');