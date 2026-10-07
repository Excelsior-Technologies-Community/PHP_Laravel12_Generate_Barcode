<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product - Barcode & QR Code Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-primary text-white rounded-top-4 p-3">
                        <h4 class="fw-bold m-0">📦 Add New Product</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('products.store') }}" method="POST">
                            @csrf
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Product Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Wireless Mouse" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="sku" class="form-label fw-semibold">SKU Code</label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                           id="sku" name="sku" value="{{ old('sku') }}" placeholder="e.g. PROD-1001" required>
                                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="price" class="form-label fw-semibold">Price ($)</label>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" placeholder="29.99" required>
                                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="stock" class="form-label fw-semibold">Stock Quantity</label>
                                    <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', 10) }}" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="barcode_type" class="form-label fw-semibold">Symbology Format</label>
                                    <select name="barcode_type" id="barcode_type" class="form-select">
                                        <option value="C128" selected>1D - Code128 (Standard)</option>
                                        <option value="C39">1D - Code39</option>
                                        <option value="EAN13">1D - EAN-13 Retail</option>
                                        <option value="QRCODE">2D - QR Code (vCard / Specs)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Product details..."></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="generate_barcode" name="generate_barcode" checked>
                                    <label class="form-check-label fw-bold" for="generate_barcode">
                                        Auto-Generate Barcode / QR Code
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('products.index') }}" class="btn btn-secondary rounded-pill px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Create Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>