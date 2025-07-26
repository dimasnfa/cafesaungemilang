<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Cafe Saung Gemilang')</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

@stack('styles')
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-white" href="{{ route('home') }}">
            <i class="fa fa-home"></i> Home
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<!-- Konten Utama -->
<main class="container mt-4">
    @yield('content')
</main>

<!-- Modal Pemilihan Tempat Duduk -->
<div class="modal fade" id="seatSelectionModal" tabindex="-1" aria-labelledby="seatSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seatSelectionModalLabel">Pilih Tempat Duduk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="seat-option" data-seat="Lesehan">
                            <div class="seat-image-placeholder bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                <i class="bi bi-house-door fs-1 text-muted"></i>
                            </div>
                            <p class="mt-2 fw-bold">Lesehan</p>
                            <button class="btn btn-outline-success check-seat" data-seat="Lesehan">
                                <i class="fa fa-check-circle"></i> Pilih
                            </button>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="seat-option" data-seat="Meja Cafe">
                            <div class="seat-image-placeholder bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                <i class="bi bi-table fs-1 text-muted"></i>
                            </div>
                            <p class="mt-2 fw-bold">Meja dan Kursi</p>
                            <button class="btn btn-outline-success check-seat" data-seat="Meja Cafe">
                                <i class="fa fa-check-circle"></i> Pilih
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        let csrfToken = $('meta[name="csrf-token"]').attr("content");

        // ✅ FIXED: Hanya handle seat selection, biarkan addtocart.js handle cart operations
        $(document).on("click", ".check-seat", function () {
            let tipeMeja = $(this).data("seat");
            $("#tableNumber").val(tipeMeja);

            let posisiTempatSelect = $("#posisi_tempat");
            if (posisiTempatSelect.length) {
                posisiTempatSelect.empty();

                if (tipeMeja === "Lesehan") {
                    posisiTempatSelect.append(`<option value="Lantai 1">Lantai 1</option>`);
                } else if (tipeMeja === "Meja Cafe") {
                    posisiTempatSelect.append(`
                        <option value="Lantai 1">Lantai 1</option>
                        <option value="Lantai 2">Lantai 2</option>
                    `);
                }
            }

            $("#seatSelectionModal").modal("hide");
        });

        // ✅ REMOVED: Cart operations - biarkan addtocart.js yang handle
        // Komentar atau hapus event handler untuk .update-qty dan .delete-cart
        // karena sudah dihandle oleh addtocart.js dengan nama class yang berbeda

        // ✅ ADDED: Global error handler untuk debug
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
        });

        // ✅ ADDED: Handle unhandled promise rejections
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
        });

        // ✅ ADDED: Utility function untuk format currency
        window.formatRupiah = function(amount) {
            if (amount === null || amount === undefined || amount === '') {
                return 'Rp 0';
            }
            return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
        };

        // ✅ ADDED: Global CSRF token helper
        window.getCsrfToken = function() {
            return $('meta[name="csrf-token"]').attr('content');
        };

        // ✅ ADDED: Global AJAX setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            error: function(xhr, status, error) {
                // Log error untuk debugging
                console.error('Global AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });

                // Handle common errors
                if (xhr.status === 419) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Session Expired',
                        text: 'Silakan refresh halaman dan coba lagi.',
                        confirmButtonText: 'Refresh',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else if (xhr.status === 500) {
                    console.error('Server Error 500:', xhr.responseText);
                    // Don't show alert for 500 errors in production
                    // Let specific handlers deal with them
                }
            }
        });

        // ✅ ADDED: Detect if addtocart.js is loaded
        setTimeout(function() {
            if (typeof window.addtocartLoaded === 'undefined') {
                console.warn('addtocart.js might not be loaded properly');
            }
        }, 1000);
    });
</script>

@stack('scripts')
</body>
</html>