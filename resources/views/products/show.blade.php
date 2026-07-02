<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Laravel Barcode Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2>Product Details</h2>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Product Information</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>ID</th>
                                        <td>{{ $product->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $product->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>SKU</th>
                                        <td>{{ $product->sku }}</td>
                                    </tr>
                                    <tr>
                                        <th>Price</th>
                                        <td>${{ number_format($product->price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{ $product->description ?: 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $product->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">

                                    <div class="badge bg-primary mb-2 px-3 py-2 fs-6">
                                        Dual Code System
                                    </div>

                                    {{-- Barcode --}}
                                    @if($product->barcode)
                                    <div class="mb-4">
                                        <h6>Barcode</h6>
                                        <img src="{{ route('product.barcode.image', $product->id) }}"
                                            class="img-fluid border p-3"
                                            style="max-width: 300px;">
                                    </div>
                                    @else
                                    <div class="alert alert-warning">
                                        No barcode generated for this product.
                                    </div>
                                    @endif

                                    {{-- QR Code --}}
                                    <div class="mb-3">
                                        <h6>QR Code</h6>
                                        {!! QrCode::size(150)->generate($product->sku) !!}
                                    </div>

                                    <p><strong>SKU:</strong> {{ $product->sku }}</p>

                                    @if($product->barcode)
                                    <a href="{{ route('product.barcode.download', $product->id) }}"
                                        class="btn btn-success">
                                        Download Barcode
                                    </a>

                                    <button onclick="window.print()" class="btn btn-dark">
                                        Print Barcode
                                    </button>
                                    @endif

                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">Edit
                                Product</a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete Product</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Barcode Generator Demo -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Barcode Generator Demo</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('barcode.generate') }}" method="GET" class="row g-3">
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="code"
                                    placeholder="Enter code to generate barcode" value="{{ $product->sku }}" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">Generate Barcode</button>
                            </div>
                        </form>

                        @if(request()->has('code'))
                        <div class="mt-4 text-center">
                            <h5>Generated Barcode for: {{ request('code') }}</h5>
                            <img src="{{ DNS1D::getBarcodePNG(request('code'), 'C128') }}" alt="Barcode"
                                class="img-fluid border p-3 mt-2">
                            <p class="mt-2"><strong>Barcode Type:</strong> CODE 128</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Barcode Activity Logs -->
                @if($product->logs && $product->logs->count())
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Barcode Activity Logs</h5>
                    </div>

                    <div class="card-body">
                        <ul class="list-group">

                            @foreach($product->logs as $log)
                            <li class="list-group-item d-flex justify-content-between align-items-center">

                                <span>
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </span>

                                <small class="text-muted">
                                    {{ $log->ip_address }} | {{ $log->created_at->format('d M Y, h:i A') }}
                                </small>

                            </li>
                            @endforeach

                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>