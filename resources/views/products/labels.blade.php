<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Printable A4 Barcode Sticker Sheet (24 Labels/Page)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', system-ui, sans-serif; }
        .label-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; padding: 20px; }
        .label-card { background: white; border: 1px dashed #0d6efd; border-radius: 8px; padding: 12px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .label-card img { max-height: 55px; width: auto; max-width: 100%; margin: 5px 0; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            .label-grid { gap: 10px; padding: 0; }
            .label-card { border: 1px solid #ccc !important; box-shadow: none !important; page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3 px-3 no-print">
            <div>
                <h3 class="fw-bold text-primary m-0"><i class="fa-solid fa-print me-2"></i>Printable Barcode Sticker Label Sheet</h3>
                <p class="text-muted small m-0">Standard A4 Grid Layout (3 Columns x 8 Rows)</p>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-primary rounded-pill px-4"><i class="fa-solid fa-print me-1"></i>Print Sticker Sheet</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary rounded-pill px-3">Back to Products</a>
            </div>
        </div>

        <div class="label-grid">
            @foreach($products as $product)
                @for($i = 0; $i < 2; $i++) <!-- Print 2 copies per product -->
                <div class="label-card">
                    <div class="fw-bold text-truncate" style="font-size: 13px;">{{ $product->name }}</div>
                    <div class="text-muted font-monospace" style="font-size: 11px;">SKU: {{ $product->sku }} | <strong class="text-dark">${{ number_format($product->price, 2) }}</strong></div>
                    
                    @if($product->barcode)
                        <img src="{{ route('product.barcode.image', $product->id) }}" alt="Barcode">
                        <div class="font-monospace text-muted" style="font-size: 10px;">{{ $product->barcode }}</div>
                    @endif
                </div>
                @endfor
            @endforeach
        </div>
    </div>
</body>
</html>
