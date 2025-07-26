<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Pemesanan Takeaway</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/formcustomer.css') }}?v={{ time() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @stack('styles')
</head>
<body>

    <div class="form-wrapper">
        <div class="form-card">
            <h2 class="form-title">🛍️ Form Pemesanan Takeaway</h2>
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
