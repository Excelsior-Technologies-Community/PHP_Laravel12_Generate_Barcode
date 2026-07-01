<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Products</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f8f9fa;
        }

        .card-stat {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        }

        .barcode-img {
            width: 180px;
        }

        .table td {
            vertical-align: middle;
        }
    </style>

</head>

<body>

    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold">
                    📦 Product Barcode Dashboard
                </h2>

                <p class="text-muted">
                    Laravel 12 Barcode Generator
                </p>

            </div>

            <div>
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Product
                </a>

                <a href="{{ route('products.export') }}" class="btn btn-success">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            </div>

        </div>

        @if(session('success'))

            <div class="alert alert-success">

                {{ session('success') }}

            </div>

        @endif

        <div class="row mb-4">

            <div class="col-md-3">

                <div class="card card-stat bg-primary text-white">

                    <div class="card-body text-center">

                        <h3>{{ $totalProducts }}</h3>

                        <p class="mb-0">

                            Total Products

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card card-stat bg-success text-white">

                    <div class="card-body text-center">

                        <h3>{{ $barcodeProducts }}</h3>

                        <p class="mb-0">

                            With Barcode

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card card-stat bg-warning text-dark">

                    <div class="card-body text-center">

                        <h3>{{ $withoutBarcode }}</h3>

                        <p class="mb-0">

                            Without Barcode

                        </p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card card-stat bg-info text-white">

                    <div class="card-body text-center">

                        <h3>{{ $todayProducts }}</h3>

                        <p class="mb-0">

                            Today's Products

                        </p>

                    </div>

                </div>

            </div>

        </div>

        <div class="card shadow-sm">

            <div class="card-header">

                <form action="{{ route('products.index') }}" method="GET">

                    <div class="row">

                        <div class="col-md-10">

                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Search Name, SKU, Barcode, Description or Price...">

                        </div>

                        <div class="col-md-2 d-grid">

                            <button class="btn btn-dark">

                                <i class="fas fa-search"></i>

                                Search

                            </button>

                        </div>

                    </div>

                </form>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-dark">

                            <tr>

                                <th>ID</th>

                                <th>Name</th>

                                <th>SKU</th>

                                <th>Price</th>

                                <th>Barcode</th>

                                <th width="220">

                                    Action

                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($products as $product)

                                <tr>

                                    <td>{{ $product->id }}</td>

                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                    </td>

                                    <td>{{ $product->sku }}</td>

                                    <td>
                                        <span class="badge bg-success">
                                            ${{ number_format($product->price, 2) }}
                                        </span>
                                    </td>

                                    <td>

                                        @if($product->barcode)

                                            <img src="{{ route('product.barcode.image', $product->id) }}"
                                                class="barcode-img img-fluid">

                                            <div class="small mt-2">
                                                {{ $product->barcode }}
                                            </div>

                                        @else

                                            <span class="badge bg-warning">
                                                No Barcode
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        <div class="btn-group">

                                            <a href="{{ route('products.show', $product->id) }}"
                                                class="btn btn-info btn-sm">

                                                <i class="fas fa-eye"></i>

                                            </a>

                                            <a href="{{ route('products.edit', $product->id) }}"
                                                class="btn btn-warning btn-sm">

                                                <i class="fas fa-edit"></i>

                                            </a>

                                            @if($product->barcode)

                                                <a href="{{ route('product.barcode.download', $product->id) }}"
                                                    class="btn btn-success btn-sm">

                                                    <i class="fas fa-download"></i>

                                                </a>

                                            @endif

                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                                onsubmit="return confirm('Delete this product?')">

                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-danger btn-sm">

                                                    <i class="fas fa-trash"></i>

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center py-5">

                                        <h5>No Products Found</h5>

                                        <p class="text-muted">

                                            Try another search keyword.

                                        </p>

                                        <a href="{{ route('products.create') }}" class="btn btn-primary">

                                            Add First Product

                                        </a>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-4 d-flex justify-content-center">

                    {{ $products->links() }}

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>