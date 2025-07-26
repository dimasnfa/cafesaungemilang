@extends('cart.dinein.master')

@section('title', 'Pesanan Berhasil')

@section('content')
    <div class="text-center mt-5">
    <h2 class="text-success"><i class="fa fa-check-circle"></i> Terima kasih atas pesanan Anda!</h2>
    <p class="mt-3">Silakan konfirmasi pembayaran <strong>cash</strong> ke kasir Cafe.</p>
    <p class="mt-2">Pesanan Anda sedang diproses.</p>

    <a href="{{ route('home') }}" class="btn btn-primary mt-4">
        <i class="fa fa-home"></i> Kembali ke Beranda
    </a>
</div>
@endsection
