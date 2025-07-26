@extends('cart.takeaway.master')

@section('title', 'Pesanan Takeaway Berhasil')

@section('content')
@php
    use Carbon\Carbon;

    $waktu = Carbon::parse($waktu_pesanan)->format('H');

    if ($waktu >= 5 && $waktu < 12) {
        $sapaan = 'Halo, selamat pagi';
    } elseif ($waktu >= 12 && $waktu < 15) {
        $sapaan = 'Halo, selamat siang';
    } elseif ($waktu >= 15 && $waktu < 18) {
        $sapaan = 'Halo, selamat sore';
    } else {
        $sapaan = 'Halo, selamat malam';
    }

    $tanggal = Carbon::parse($waktu_pesanan)->locale('id')->isoFormat('D MMMM YYYY');
    $jam = Carbon::parse($waktu_pesanan)->format('H:i');

    $pesan = "$sapaan, saya $nama_pelanggan ingin konfirmasi pesanan takeaway :\n" .
             "Nama : $nama_pelanggan\n" .
             "Tanggal pesanan : $tanggal\n" .
             "Waktu ambil pesanan : $jam WIB\n" .
             "No WhatsApp : $nomor_wa\n" .
            "Metode pembayaran : QRIS\n\n" .
             "Terimakasih";

    $urlWa = 'https://wa.me/6287710349513?text=' . urlencode($pesan);
@endphp

<div class="text-center mt-5">
    <h1 class="text-success">
        <i class="fa fa-check-circle"></i> Pesanan Takeaway Anda Berhasil!
    </h1>
    <p class="lead">
        Terima kasih telah memesan.<br>
        Silakan hubungi kasir kami via WhatsApp untuk konfirmasi pesanan Anda.
    </p>

    <a href="{{ $urlWa }}" class="btn btn-success mt-4" target="_blank">
        <i class="fab fa-whatsapp"></i> Konfirmasi via WhatsApp
    </a>
</div>

@endsection