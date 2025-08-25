@extends('cart.takeaway.master')

@section('title', 'Keranjang Takeaway')

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
    <input type="hidden" id="session-wa" value="{{ session('takeaway.nomor_wa') }}">
    <input type="hidden" id="session-jenis" value="{{ session('jenis_pesanan') }}">
    <input type="hidden" id="session-nama" value="{{ session('takeaway.nama_pelanggan') }}">

    <div class="container">
        <div class="cart-header text-center my-4">
            <h2>
                <img src="{{ asset('assets/img/cart.png') }}" alt="Keranjang" class="cart-icon">
                Keranjang Takeaway
            </h2>
        </div>

        <div id="cart-content">
            @if(session('jenis_pesanan') === 'takeaway' && isset($carts) && count($carts) > 0)
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
                        @include('cart.takeaway.cart_items', ['cartItems' => $carts])
                    </tbody>
                </table>
            </div>

            <div id="payment-section" class="card mt-4 p-3 shadow-sm">
                <h2 class="text-center fw-bold">Detail Pembayaran</h2>
                <div id="order-summary">
                    @include('cart.takeaway.order_summary', ['carts' => $carts, 'total' => $total])
                </div>

                <button id="pay-button" class="btn btn-success w-100 mt-3">
                    <i class="bi bi-qr-code-scan me-2"></i> Bayar dengan QRIS
                </button>
            </div>

            <div class="text-center mt-4">
                <a href="{{ custom_route('booking', ['jenis' => 'takeaway']) }}" 
                   class="btn btn-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Kembali ke Halaman Booking Takeaway
                </a>
            </div>
            @else
            <div id="empty-cart-display" class="text-center">
                @if(session('jenis_pesanan') !== 'takeaway')
                    <p class="text-danger">Keranjang kosong. Silakan isi data pelanggan takeaway terlebih dahulu.</p>
                @else
                    <p class="text-danger">Keranjang kosong. Silakan pilih menu terlebih dahulu.</p>
                @endif
            </div>
            <div class="text-center mt-4">
                <a href="{{ custom_route('booking', ['jenis' => 'takeaway']) }}" 
                   class="btn btn-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Kembali ke Halaman Booking Takeaway
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/addtocart.js') }}"></script>

<script>
// Global variable untuk menyimpan order_id
let currentOrderId = null;

$('#pay-button').click(function () {
    $(this).prop('disabled', true).text('Memproses QRIS...');
    
    $.ajax({
        url: '{{ custom_route("cart.takeaway.checkout.process") }}',
        type: 'POST',
        data: { payment_type: 'qris' },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            console.log('✅ Checkout takeaway response:', response);
            
            if (response.snap_token && response.order_id) {
                currentOrderId = response.order_id;
                
                // ✅ KUNCI: Custom Snap pay dengan override onSuccess dan onPending (sama seperti dine-in)
                window.snap.pay(response.snap_token, {
                    onSuccess: function (result) {
                        console.log('✅ Snap onSuccess triggered for takeaway', result);
                        handleQrisSuccess();
                    },
                    onPending: function (result) {
                        console.log('⏳ Snap onPending triggered for takeaway', result);
                        handleQrisSuccess();
                    },
                    onError: function (result) {
                        console.log('❌ Snap onError triggered for takeaway', result);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Pembayaran',
                            text: 'Terjadi kesalahan saat memproses QRIS takeaway.',
                            confirmButtonText: 'OK'
                        });
                        $('#pay-button').prop('disabled', false).text('Bayar dengan QRIS');
                    },
                    onClose: function () {
                        console.log('🚪 Snap modal ditutup for takeaway');
                        $('#pay-button').prop('disabled', false).text('Bayar dengan QRIS');
                    }
                });
            } else if (response.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error,
                    confirmButtonText: 'OK'
                });
                $('#pay-button').prop('disabled', false).text('Bayar dengan QRIS');
            }
        },
        error: function (xhr) {
            console.error('Error checkout takeaway process:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error Koneksi',
                text: 'Terjadi kesalahan saat koneksi ke server.',
                confirmButtonText: 'OK'
            });
            $('#pay-button').prop('disabled', false).text('Bayar dengan QRIS');
        }
    });
});

// ✅ FUNGSI BARU: Handle QRIS Success dengan MidtransPolling (sama seperti dine-in)
function handleQrisSuccess() {
    if (!currentOrderId) {
        console.error('❌ No order ID available for takeaway');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Order ID tidak tersedia',
            confirmButtonText: 'OK'
        });
        return;
    }

    console.log('🎯 Processing QRIS takeaway success for order:', currentOrderId);
    
    // Show loading
    Swal.fire({
        title: 'Memproses Pembayaran Takeaway...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // ✅ KUNCI: Panggil MidtransPollingController untuk create pesanan takeaway
    $.ajax({
        url: `/admin/midtrans/cek-status/${currentOrderId}`,
        type: 'GET',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            console.log('✅ MidtransPooling takeaway response:', response);
            
            // Close loading
            Swal.close();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Takeaway Berhasil!',
                text: 'Pesanan takeaway Anda sedang diproses oleh kasir.',
                confirmButtonText: 'OK'
            }).then(() => {
                // Redirect akan di-handle oleh server response
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    // Fallback redirect untuk takeaway
                    window.location.href = "{{ custom_route('cart.takeaway.checkout.success') }}";
                }
            });
        },
        error: function (xhr) {
            console.error('❌ MidtransPooling takeaway error:', xhr.responseText);
            
            // Close loading
            Swal.close();
            
            // Show error but still redirect (because payment was successful)
            Swal.fire({
                icon: 'warning',
                title: 'Pembayaran Berhasil',
                text: 'Pembayaran QRIS berhasil'
                confirmButtonText: 'Lanjutkan'
            }).then(() => {
                // Fallback: langsung redirect ke success takeaway
                window.location.href = "{{ custom_route('cart.takeaway.checkout.success') }}";
            });
        }
    });
}

// ✅ Debug log
console.log('🔧 Cart takeaway page initialized');
console.log('📋 Session data:', {
    jenis: $('#session-jenis').val(),
    wa: $('#session-wa').val(),
    nama: $('#session-nama').val()
});
</script>
@endsection