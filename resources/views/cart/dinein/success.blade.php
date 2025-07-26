@extends('cart.dinein.master')

@section('title', 'Pesanan Berhasil')

@section('content')
    <div class="text-center mt-5">
        <h1 class="text-success"><i class="fa fa-check-circle"></i> Pesanan Anda Berhasil!</h1>
        <p class="lead">Terima kasih telah memesan. Silakan tunggu pesanan Anda di meja.</p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-4">
            <i class="fa fa-home"></i> Kembali ke Beranda
        </a>
    </div>
@endsection
