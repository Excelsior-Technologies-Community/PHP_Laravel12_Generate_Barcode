<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Barcode & QR Code Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-primary text-white rounded-top-4 p-3">
                        <h4 class="fw-bold m-0">✏️ Edit Product: {{ $product->name }}</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('products.update', $product->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Product Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="sku" class="form-label fw-semibold">SKU Code</label>
                                    <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="price" class="form-label fw-semibold">Price ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="stock" class="form-label fw-semibold">Stock Quantity</label>
                                    <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="barcode_type" class="form-label fw-semibold">Symbology Format</label>
                                    <select name="barcode_type" id="barcode_type" class="form-select">
                                        <option value="C128" {{ ($product->barcode_type ?? 'C128') == 'C128' ? 'selected' : '' }}>1D - Code128 (Standard)</option>
                                        <option value="C39" {{ ($product->barcode_type ?? '') == 'C39' ? 'selected' : '' }}>1D - Code39</option>
                                        <option value="EAN13" {{ ($product->barcode_type ?? '') == 'EAN13' ? 'selected' : '' }}>1D - EAN-13 Retail</option>
                                        <option value="QRCODE" {{ ($product->barcode_type ?? '') == 'QRCODE' ? 'selected' : '' }}>2D - QR Code (vCard / Specs)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="regenerate_barcode" name="regenerate_barcode">
                                    <label class="form-check-label fw-bold" for="regenerate_barcode">
                                        Regenerate Barcode / QR Code
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('products.index') }}" class="btn btn-secondary rounded-pill px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Update Product</button>
                            </div>
                        </form>
                    </div>
                </div>

                @if($product->barcode)
                <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                    <h5 class="fw-bold mb-3">Current Active Barcode / QR Symbology</h5>
                    <div>
                        <img src="{{ route('product.barcode.image', $product->id) }}" alt="Barcode" class="img-fluid border p-3 rounded" style="max-height: 120px;">
                    </div>
                    <p class="mt-2 font-monospace text-muted mb-3">Code: {{ $product->barcode }} ({{ $product->barcode_type ?? 'C128' }})</p>
                    <div>
                        <a href="{{ route('product.barcode.download', $product->id) }}" class="btn btn-success rounded-pill px-4">
                            <i class="fas fa-download me-1"></i> Download Symbology PNG
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>