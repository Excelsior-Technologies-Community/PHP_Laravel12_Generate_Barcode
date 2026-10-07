<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products & Barcode / QR Code Studio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Html5-QRCode Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .card-stat {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
            transition: transform 0.2s ease;
        }

        .card-stat:hover {
            transform: translateY(-3px);
        }

        .barcode-img {
            max-height: 55px;
            width: auto;
        }

        .table td {
            vertical-align: middle;
        }

        #reader {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            border: 2px dashed #0d6efd;
        }
    </style>
</head>

<body>

    <div class="container py-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="fw-bold text-dark m-0">
                    📦 Product Barcode & QR Code Studio
                </h2>
                <p class="text-muted small m-0">
                    Multi-Symbology Generator, Web Camera Scanner & Printable Label Sheets
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#scannerModal" onclick="startCameraScanner()">
                    <i class="fas fa-camera me-1"></i> Live Web Cam Scanner
                </button>

                <a href="{{ route('labels.print') }}" target="_blank" class="btn btn-warning rounded-pill px-3">
                    <i class="fas fa-print me-1"></i> Label Sheet Studio
                </a>

                <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i> Add Product
                </a>

                <div class="btn-group">
                    <button class="btn btn-outline-success dropdown-toggle rounded-pill px-3" data-bs-toggle="dropdown">
                        <i class="fas fa-file-export me-1"></i> Exporter Studio
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a href="{{ route('products.export') }}" class="dropdown-item"><i class="fas fa-file-csv text-success me-2"></i> Export CSV</a></li>
                        <li><a href="{{ route('products.bulk.zip') }}" class="dropdown-item"><i class="fas fa-file-archive text-primary me-2"></i> Bulk Barcodes Zip Archive</a></li>
                        <li><a href="{{ route('scan.logs') }}" class="dropdown-item"><i class="fas fa-list-check text-warning me-2"></i> Scan Audit Logs</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a href="{{ route('api.products') }}" target="_blank" class="dropdown-item"><i class="fas fa-code text-info me-2"></i> Restful JSON API</a></li>
                    </ul>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-3 mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger rounded-3 mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-stat bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="fw-bold">{{ $totalProducts }}</h3>
                        <p class="mb-0 small fw-bold">TOTAL PRODUCTS</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-stat bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="fw-bold">{{ $barcodeProducts }}</h3>
                        <p class="mb-0 small fw-bold">WITH BARCODE / QR</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-stat bg-warning text-dark">
                    <div class="card-body text-center">
                        <h3 class="fw-bold">{{ $totalStock }}</h3>
                        <p class="mb-0 small fw-bold">TOTAL INVENTORY STOCK</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-stat bg-info text-white">
                    <div class="card-body text-center">
                        <h3 class="fw-bold">{{ $todayProducts }}</h3>
                        <p class="mb-0 small fw-bold">TODAY'S PRODUCTS</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Card -->
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body p-3">
                <form action="{{ route('products.index') }}" method="GET">
                    <div class="row g-2">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-0"
                                    placeholder="Search Product Name, SKU, Barcode Code, Price, or Description...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-dark w-100 rounded-pill fw-bold">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Product Details</th>
                                <th>SKU</th>
                                <th>Price & Stock</th>
                                <th>Barcode / QR Symbology</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>#{{ $product->id }}</td>
                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                        @if($product->description)
                                            <br><small class="text-muted">{{ Str::limit($product->description, 40) }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary font-monospace">{{ $product->sku }}</span></td>
                                    <td>
                                        <div class="fw-bold text-success">${{ number_format($product->price, 2) }}</div>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="badge bg-light text-dark border">Stock: <strong id="stock-{{ $product->id }}">{{ $product->stock }}</strong></span>
                                            <button class="btn btn-sm btn-outline-success py-0 px-1" onclick="quickAdjustStock({{ $product->id }}, 1)" title="Add Stock +1">+</button>
                                            <button class="btn btn-sm btn-outline-danger py-0 px-1" onclick="quickAdjustStock({{ $product->id }}, -1)" title="Reduce Stock -1">-</button>
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->barcode)
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ route('product.barcode.image', $product->id) }}" class="barcode-img img-fluid">
                                                <span class="badge bg-primary rounded-pill small">{{ $product->barcode_type ?? 'C128' }}</span>
                                            </div>
                                            <div class="small text-muted font-monospace mt-1">{{ $product->barcode }}</div>
                                        @else
                                            <span class="badge bg-warning text-dark">No Barcode</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-info btn-sm text-white" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($product->barcode)
                                                <a href="{{ route('product.barcode.download', $product->id) }}" class="btn btn-success btn-sm" title="Download Image">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Delete product?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <h5 class="text-muted">No Products Found</h5>
                                        <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill mt-2">
                                            Add First Product
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->lastPage() > 1)
                <div class="p-3 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Live Web Camera Barcode / QR Scanner Modal -->
    <div class="modal fade" id="scannerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-dark text-white rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-camera text-warning me-2"></i>Live Web Camera Scanner Studio</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="stopCameraScanner()"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="text-muted small">Point your laptop or mobile camera at any physical Barcode or QR Code</p>

                    <div id="reader" class="mb-3"></div>

                    <div id="scanResult" class="alert alert-success d-none text-start">
                        <h6 class="fw-bold m-0" id="resTitle">Product Detected!</h6>
                        <div id="resBody" class="small mt-1"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" onclick="stopCameraScanner()">Close Scanner</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let html5QrCode = null;

        function startCameraScanner() {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    const cameraId = devices[0].id;
                    html5QrCode.start(
                        cameraId,
                        { fps: 10, qrbox: { width: 250, height: 180 } },
                        (decodedText, decodedResult) => {
                            playPosBeep();
                            onCodeScanned(decodedText);
                        },
                        (errorMessage) => {
                            // parse error
                        }
                    );
                }
            }).catch(err => {
                alert("Camera access denied or unavailable.");
            });
        }

        function stopCameraScanner() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    console.log("Scanner stopped.");
                });
            }
        }

        function onCodeScanned(code) {
            fetch('/scan/lookup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: code })
            })
            .then(res => res.json())
            .then(data => {
                const resDiv = document.getElementById('scanResult');
                resDiv.classList.remove('d-none');

                if (data.success) {
                    document.getElementById('resTitle').innerText = "✅ Product Detected: " + data.product.name;
                    document.getElementById('resBody').innerHTML = `
                        <strong>SKU:</strong> ${data.product.sku} | <strong>Price:</strong> $${data.product.price} | <strong>Stock:</strong> ${data.product.stock}<br>
                        <a href="/products/${data.product.id}" class="btn btn-sm btn-primary mt-2">Open Product Details</a>
                    `;
                } else {
                    document.getElementById('resTitle').innerText = "⚠️ " + data.message;
                    document.getElementById('resBody').innerText = "Code scanned: " + code;
                }
            });
        }

        function quickAdjustStock(productId, amount) {
            fetch('/scan/stock', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId, amount: amount })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('stock-' + productId).innerText = data.new_stock;
                }
            });
        }

        function playPosBeep() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(1200, audioCtx.currentTime); // High POS Beep pitch
                gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.15);
            } catch(e) {}
        }
    </script>
</body>

</html>