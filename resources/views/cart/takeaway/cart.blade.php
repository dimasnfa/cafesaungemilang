@extends('cart.takeaway.master')

@section('title', 'Keranjang Takeaway')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">

 {{-- Sandbox Midtrans  --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

{{--  Production Midtrans --}}
{{-- <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script> --}}

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="cart-page">
    <input type="hidden" id="session-wa" value="{{ session('takeaway.nomor_wa') }}">
    <input type="hidden" id="session-jenis" value="{{ session('jenis_pesanan') }}">

    <div class="container">
        <div class="cart-header text-center mb-4">
            <h2>
                <img src="{{ asset('assets/img/cart.png') }}" alt="Keranjang" class="cart-icon">
                Keranjang Takeaway
            </h2>
        </div>

        @if(session('jenis_pesanan') != 'takeaway')
            <p class="text-center text-danger">Keranjang kosong. Silakan isi data pelanggan takeaway terlebih dahulu.</p>
        @else
            @if(isset($carts) && count($carts) > 0)
                <table class="table table-bordered cart-table text-center">
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

                <!-- Bagian ini tidak akan hilang karena tidak ikut terganti -->
                <div id="payment-section" class="card mt-4 p-3 shadow-sm">
                    <h2 class="text-center fw-bold">Detail Pembayaran</h2>
                    <div id="order-summary">
                        @include('cart.takeaway.order_summary', ['carts' => $carts, 'total' => $total])
                    </div>

                    <button id="pay-button" class="btn btn-success w-100 mt-3 d-flex justify-content-center align-items-center">
                        <i class="bi bi-qr-code-scan me-2 fs-5"></i> Bayar dengan QRIS
                    </button>
                </div>
            @else
                <p class="text-center text-danger">Tidak ada data di keranjang</p>
            @endif

            <div class="text-center mt-3">
                <a href="{{ route('booking', ['jenis' => 'takeaway']) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Kembali ke Halaman Booking Takeaway
                </a>
            </div>
        @endif
    </div>
</div>

<script src="{{ asset('assets/js/addtocart.js') }}"></script>

<script>
    $('#pay-button').click(function (e) {
        e.preventDefault();
        $(this).prop('disabled', true).html('<i class="bi bi-qr-code-scan me-2"></i> Memproses QRIS...');

        $.ajax({
            url: "{{ custom_route('cart.takeaway.checkout.process') }}",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                payment_type: 'qris'
            },
            success: function (res) {
                if (res.snap_token) {
                    snap.pay(res.snap_token, {
                        onSuccess: function () {
                            window.location.href = "{{ custom_route('cart.takeaway.checkout.success') }}";
                        },
                        onPending: function () {
                            window.location.href = "{{ custom_route('cart.takeaway.checkout.success') }}";
                        },
                        onError: function () {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat memproses QRIS.', 'error');
                            $('#pay-button').prop('disabled', false).html('<i class="bi bi-qr-code-scan me-2"></i> Bayar dengan QRIS');
                        },
                        onClose: function () {
                            $('#pay-button').prop('disabled', false).html('<i class="bi bi-qr-code-scan me-2"></i> Bayar dengan QRIS');
                        }
                    });
                } else {
                    Swal.fire('Error', res.error || 'Token tidak tersedia', 'error');
                    $('#pay-button').prop('disabled', false).html('<i class="bi bi-qr-code-scan me-2"></i> Bayar dengan QRIS');
                }
            },
            error: function () {
                Swal.fire('Error', 'Terjadi kesalahan saat koneksi ke server.', 'error');
                $('#pay-button').prop('disabled', false).html('<i class="bi bi-qr-code-scan me-2"></i> Bayar dengan QRIS');
            }
        });
    });
</script>
@endsection