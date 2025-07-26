@extends('cart.dinein.master')

@section('title', 'Keranjang Dine-In')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 untuk konsistensi dengan addtocart.js -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Sandbox Midtrans  --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

{{--  Production Midtrans --}}
{{-- <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script> --}}

<div class="cart-page">
    <!-- Session data untuk JavaScript -->
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

        @if(isset($carts) && count($carts) > 0)
        <div class="table-responsive">
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

        <!-- Bagian ini tidak akan hilang karena tidak ikut terganti -->
        <div id="payment-section" class="card mt-4 p-3 shadow-sm">
            <h2 class="text-center fw-bold">Detail Pembayaran</h2>
            <div id="order-summary">
                @include('cart.dinein.order_summary', ['carts' => $carts, 'total' => $total])
            </div>

            <!-- Tombol Bayar -->
            <button id="pay-button" class="btn btn-success w-100 mt-3">Bayar Sekarang</button>
        </div>

        <!-- Kembali -->
        <div class="text-center mt-4">
            <a href="{{ session('meja_id') ? url("dinein/booking/" . session('meja_id') . "?from_qr=yes") : custom_route('booking', ['jenis' => 'dinein']) }}"
               class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle"></i> Kembali ke Halaman Booking
            </a>
        </div>
        @else
        <div class="text-center">
            <p class="text-danger">Keranjang kosong. Silakan pilih menu terlebih dahulu.</p>
            <div class="mt-3">
                <span id="cart-count" class="badge bg-secondary">0</span>
            </div>
        </div>
        @endif
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

<!-- ✅ FIXED: Include addtocart.js untuk handling cart operations -->
<script src="{{ asset('assets/js/addtocart.js') }}"></script>

<!-- Script untuk payment processing -->
<script>
$('#pay-button').click(function () {
    $('#paymentModal').modal('show');
});

$('#choose-qris').click(function () {
    $('#paymentModal').modal('hide');
    $('#pay-button').prop('disabled', true).text('Memproses QRIS...');

    $.ajax({
        url: '{{ custom_route("cart.dinein.checkout.process") }}',
        type: 'POST',
        data: { payment_type: 'qris' },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            if (response.snap_token) {
                snap.pay(response.snap_token, {
                    onSuccess: function () { 
                        window.location.href = "{{ custom_route('cart.dinein.checkout.success') }}";
                    },
                    onPending: function () {
                        window.location.href = "{{ custom_route('cart.dinein.checkout.success') }}";
                    },
                    onError: function () {
                        alert('Terjadi kesalahan saat memproses QRIS.');
                        $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
                    },
                    onClose: function () {
                        $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
                    }
                });
            } else if (response.error) {
                alert(response.error);
                $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
            }
        },
        error: function () {
            alert('Terjadi kesalahan saat koneksi ke server.');
            $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
        }
    });
});

$('#choose-cash').click(function () {
    $('#paymentModal').modal('hide');
    $('#pay-button').prop('disabled', true).text('Memproses Cash...');

    $.ajax({
        url: '{{ custom_route("cart.dinein.checkout.cash") }}',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            if (response.success && response.redirect_url) {
                window.location.href = response.redirect_url;
            } else if (response.error) {
                alert(response.error);
                $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert('Terjadi kesalahan saat memproses pembayaran.');
            $('#pay-button').prop('disabled', false).text('Bayar Sekarang');
        }
    });
});
</script>
@endsection