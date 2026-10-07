<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Audit Trail Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        .card { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark m-0"><i class="fa-solid fa-barcode text-primary me-2"></i>Barcode Scan Audit Trail Logs</h2>
                <p class="text-muted small m-0">Live history of Web Camera & Scanner barcode lookups</p>
            </div>
            <div>
                <a href="{{ route('products.index') }}" class="btn btn-secondary rounded-pill">Back to Dashboard</a>
            </div>
        </div>

        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Scanned Code</th>
                            <th>Product Name</th>
                            <th>Action Taken</th>
                            <th>Device IP</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scanLogs as $log)
                        <tr>
                            <td class="fw-bold font-monospace text-primary">{{ $log->scanned_code }}</td>
                            <td>
                                @if($log->product)
                                    <strong>{{ $log->product->name }}</strong> <small class="text-muted">({{ $log->product->sku }})</small>
                                @else
                                    <span class="badge bg-secondary">Unknown Code</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ str_contains($log->action_taken, '+') ? 'bg-success' : (str_contains($log->action_taken, '-') ? 'bg-warning text-dark' : 'bg-info') }}">
                                    {{ $log->action_taken }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $log->device_ip }}</small></td>
                            <td>{{ $log->created_at->format('d M Y h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No barcode scan logs recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($scanLogs->lastPage() > 1)
            <div class="mt-3 d-flex justify-content-center">
                {{ $scanLogs->links() }}
            </div>
            @endif
        </div>
    </div>
</body>
</html>
