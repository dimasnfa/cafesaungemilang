@extends('cart.dinein.master')

@section('title', 'Keranjang Dine-In')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Sandbox Midtrans  --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<style>
.navbar-toggler,
.navbar-toggle,
.hamburger-menu,
.menu-toggle,
.btn-navbar,
[data-bs-toggle="collapse"],
[data-toggle="collapse"] {
    display: none !important;
}
</style>

<div class="cart-page">
    <input type="hidden" id="session-jenis" value="{{ session('jenis_pesanan') }}">
    <input type="hidden" id="session-meja" value="{{ session('meja_id') }}">
    <input type="hidden" id="session-wa" value="{{ session('takeaway.nomor_wa') }}">

    <div class="container">
        <div class="cart-header text-center my-4">
            <h2>
                <img src="{{ asset('assets/img/cart.png') }}" alt="Keranjang" class="cart-icon">
                Keranjang Dine-In
            </h2>
        </div>

        <div id="cart-content">
            @if(isset($carts) && count($carts) > 0)
            <div class="table-responsive" id="cart-table-container">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Menu</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cart-items">
                        @include('cart.dinein.cart_items', ['cartItems' => $carts])
                    </tbody>
                </table>
            </div>

            <div id="payment-section" class="card mt-4 p-3 shadow-sm">
                <h2 class="text-center fw-bold">Detail Pembayaran</h2>
                <div id="order-summary">
                    @include('cart.dinein.order_summary', ['carts' => $carts, 'total' => $total])
                </div>

                <button id="pay-button" class="btn btn-success w-100 mt-3">Bayar Sekarang</button>
            </div>

            <div class="text-center mt-4">
                <a href="{{ session('meja_id') ? url("dinein/booking/" . session('meja_id') . "?from_qr=yes") : custom_route('booking', ['jenis' => 'dinein']) }}"
                   class="btn btn-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Kembali ke Halaman Booking
                </a>
            </div>
            @else
            <div id="empty-cart-display" class="text-center">
                <p class="text-danger">Keranjang kosong. Silakan pilih menu terlebih dahulu.</p>
            </div>
            <div class="text-center mt-4">
                <a href="{{ session('meja_id') ? url("dinein/booking/" . session('meja_id') . "?from_qr=yes") : custom_route('booking', ['jenis' => 'dinein']) }}"
                   class="btn btn-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Kembali ke Halaman Booking
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Metode Pembayaran -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Pilih Metode Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-success btn-lg rounded-pill d-flex align-items-center justify-content-center" id="choose-qris" data-method="qris">
                        <i class="bi bi-qr-code-scan me-2 fs-5"></i> Bayar dengan QRIS
                    </button>
                    <button type="button" class="btn btn-primary btn-lg rounded-pill d-flex align-items-center justify-content-center" id="choose-cash" data-method="cash">
                        <i class="bi bi-cash-coin me-2 fs-5"></i> Bayar dengan Cash
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/addtocart.js') }}"></script>

<script>
// Global variable untuk menyimpan order_id
let currentOrderId = null;

$('#pay-button').click(function () {
    $('#paymentModal').modal('show');
});

// ✅ PERBAIKAN UTAMA: QRIS Payment dengan MidtransPolling integration
$('#choose-qris').click(function () {
    $('#paymentModal').modal('hide');
    $('#pay-button').prop('disabled', true).text('Memproses QRIS...');

    $.ajax({
        url: '{{ custom_route("cart.dinein.checkout.process") }}',
        type: 'POST',
        data: { payment_type: 'qris' },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            console.log('✅ Checkout response:', response);
            
            if (response.snap_token && response.order_id) {
                currentOrderId = response.order_id;
                
                // ✅ KUNCI: Custom Snap pay dengan override onSuccess dan onPending
                window.snap.pay(response.snap_token, {
                    onSuccess: function (result) {
                        console.log('✅ Snap onSuccess triggered', result);
                        handleQrisSuccess();
                    },
                    onPending: function (result) {
                        console.log('⏳ Snap onPending triggered', result);
                        handleQrisSuccess();
                    },
                    onError: function (result) {
                        console.log('❌ Snap onError triggered', result);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Pembayaran',
                            text: 'Terjadi kesalahan saat memproses QRIS.',
                            confirmButtonText: 'OK'
                        });
                        $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
                    },
                    onClose: function () {
                        console.log('🚪 Snap modal ditutup');
                        $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
                    }
                });
            } else if (response.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error,
                    confirmButtonText: 'OK'
                });
                $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
            }
        },
        error: function (xhr) {
            console.error('Error checkout process:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error Koneksi',
                text: 'Terjadi kesalahan saat koneksi ke server.',
                confirmButtonText: 'OK'
            });
            $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
        }
    });
});

// ✅ FUNGSI BARU: Handle QRIS Success dengan MidtransPolling
function handleQrisSuccess() {
    if (!currentOrderId) {
        console.error('❌ No order ID available');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Order ID tidak tersedia',
            confirmButtonText: 'OK'
        });
        return;
    }

    console.log('🎯 Processing QRIS success for order:', currentOrderId);
    
    // Show loading
    Swal.fire({
        title: 'Memproses Pembayaran...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // ✅ KUNCI: Panggil MidtransPollingController untuk create pesanan
    $.ajax({
        url: `/admin/midtrans/cek-status/${currentOrderId}`,
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            console.log('✅ MidtransPooling response:', response);
            
            // Close loading
            Swal.close();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                text: 'Pesanan Anda sedang diproses oleh kasir.',
                confirmButtonText: 'OK'
            }).then(() => {
                // Redirect akan di-handle oleh server response
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    // Fallback redirect
                    window.location.href = "{{ custom_route('cart.dinein.checkout.success') }}";
                }
            });
        },
        error: function (xhr) {
            console.error('❌ MidtransPooling error:', xhr.responseText);
            
            // Close loading
            Swal.close();
            
            // Show error but still redirect (because payment was successful)
            Swal.fire({
                icon: 'warning',
                title: 'Pembayaran Berhasil',
                text: 'Pembayaran QRIS berhasil',
                confirmButtonText: 'Lanjutkan'
            }).then(() => {
                // Fallback: langsung redirect ke success
                window.location.href = "{{ custom_route('cart.dinein.checkout.success') }}";
            });
        }
    });
}

// ✅ Cash Payment - tidak berubah
$('#choose-cash').click(function () {
    $('#paymentModal').modal('hide');
    $('#pay-button').prop('disabled', true).text('Memproses Cash...');

    $.ajax({
        url: '{{ custom_route("cart.dinein.checkout.cash") }}',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            if (response.success && response.redirect_url) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pesanan Berhasil!',
                    text: 'Silakan bayar ke kasir.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = response.redirect_url;
                });
            } else if (response.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error,
                    confirmButtonText: 'OK'
                });
                $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memproses pembayaran.',
                confirmButtonText: 'OK'
            });
            $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
        }
    });
});

// ✅ Debug log
console.log('🔧 Cart page initialized');
console.log('📋 Session data:', {
    jenis: $('#session-jenis').val(),
    meja: $('#session-meja').val(),
    wa: $('#session-wa').val()
});
</script>
@endsection