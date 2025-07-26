@extends('landing.components.layout')

@section('title', 'Home - Gemilang Cafe')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<input type="hidden" id="session-jenis" value="{{ session('jenis_pesanan') }}">
<input type="hidden" id="session-meja" value="{{ session('meja_id') }}">
<input type="hidden" id="session-wa" value="{{ session('takeaway.nomor_wa') }}">

<!-- Hero Section Start -->
<div class="container-fluid py-6 my-6 mt-0" style="background-image: url('{{ asset('assets/img/banner/banner-gemilang.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="container text-center animated bounceInDown" style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px;">
        <h1 class="display-1 mb-4">Dine-in</h1>
        <ol class="breadcrumb justify-content-center mb-0 animated bounceInDown">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item text-dark" aria-current="page">Dine-in</li>
        </ol>
    </div>
</div>
<!-- Hero Section End -->

<!-- Pemanggilan CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/booking.css') }}">

{{-- Rekomendasi Menu --}}
<div class="d-flex flex-column align-items-center mb-3">
    <div class="rekomendasi-frame">
        <a href="{{ route('menu.rekomendasi', ['tipe' => 'dinein']) }}">
            <i class="fas fa-utensils"></i>
            <span>Rekomendasi Menu</span>
        </a>
    </div>
</div>

<!-- Booking Section Start -->
<div class="booking-section">
    <div class="container text-center">
        <h1 class="section-title display-4">Gemilang Cafe & Saung</h1>
        <p class="section-subtitle">Pesan menu favorit Anda dengan mudah dan cepat!</p>
    </div>

    <div class="container">
        <h2 class="text-center text-dark fw-bold mb-4">Daftar Menu</h2>

        <!-- Kategori Menu -->
        <div class="category-nav text-center mb-4">
            @foreach ($kategoris as $kategori)
                <a href="#{{ str_replace(' ', '-', strtolower($kategori->nama_kategori)) }}" class="btn btn-outline-primary m-1">
                    {{ $kategori->nama_kategori }}
                </a>
            @endforeach
        </div>

        @foreach ($kategoris as $kategori)
            <h4 id="{{ str_replace(' ', '-', strtolower($kategori->nama_kategori)) }}" class="category-title mt-4">
                {{ $kategori->nama_kategori }}
            </h4>

            <div class="row">
                @foreach ($kategori->menus as $menu)
                    @php
                        $folder = strtolower(str_replace([' ', '&'], '-', $kategori->nama_kategori));
                        $defaultGambar = 'default.png';
                        
                        $customImages = [
                            'Makanan' => [
                                'Nasi' => 'nasi.png',
                                'Ayam Goreng' => 'ayamgoreng.jpg',
                                'Ayam Bakar' => 'ayambakar.jpg',
                                'Ayam Mentega' => 'ayamentega.jpg',
                                'Ayam Lada Hitam' => 'ayamladahitam.jpg',
                                'Ayam Lombok Ijo' => 'ayamlombokijo.jpg',
                                'Ayam Asam Manis' => 'ayamasammanis.jpg',
                                'Ayam Saos Padang' => 'ayamsaospadang.jpg',
                                'Ayam Rica-Rica' => 'ayamricarica.jpg',
                                'Sop Ayam' => 'sopayam.jpg',
                                'Garang Asem Ayam' => 'garangasem.jpg',
                                'Sambal Mentah' => 'sambalmentah.jpg',
                                'Sambal Pecak' => 'sambalpecak.jpg',
                                'Sambal Terasi' => 'sambalterasi.jpg',
                                'Sambal Geprek' => 'sambalgeprek.jpg',
                                'Sambal Bawang' => 'sambalbawang.jpg',
                                'Sambal Ijo' => 'sambalijo.jpg',
                                'Sambal Dabu-Dabu' => 'sambaldabu.jpg',
                            ],
                            'Minuman' => [
                                'Jus Alpukat' => 'alpukat.jpg',
                                'Jus Apel' => 'apel.jpg',
                                'Jus Strawberry' => 'strawberry.jpg',
                                'Jus Jeruk' => 'jeruk.jpg',
                                'Jus Tomat' => 'tomat.jpg',
                                'Jus Mangga' => 'mangga.jpg',
                                'Jus Melon' => 'melon.jpg',
                                'Jus Fibar' => 'fibar.jpg',
                                'Jus Wortel' => 'wortel.jpg',
                                'Jeruk Panas' => 'jerukpanas.jpg',
                                'Jeruk Dingin' => 'jerukdingin.jpg',
                                'Teh Manis Panas' => 'tehmanis.jpg',
                                'Teh Manis Dingin' => 'tehmanis.jpg',
                                'Coffe Ekspresso' => 'coffekspresso.jpg',
                                'Cappucino Ice' => 'cappucinoice.jpg',
                                'Cappucino Hot' => 'cappucinohot.jpg',
                                'Cofe Susu Gula Aren' => 'coffesusugularen.jpg',
                                'Best Latte Ice' => 'bestlattehot.jpg',
                                'Cofe Latte Ice' => 'coffelatteice.jpg',
                                'Cofe Latte Hot' => 'coffelatehot.jpg',
                                'Matcha Ice' => 'macthaice.jpg',
                                'Matcha Hot' => 'matchahot.jpg',
                                'Coklat Ice' => 'coklatice.jpg',
                                'Coklat Hot' => 'coklathot.jpg',
                                'Red Valvet Ice' => 'redvlvt.jpg',
                                'Red Valvet Hot' => 'redvlvt.jpg',
                                'Vakal Peach' => 'vakalpeach.jpg',
                                'Beauty Peach' => 'beautypeach.jpg',
                                'Teh Tubruk' => 'tehtubruk.jpg',
                                'Teh Tubruk Susu' => 'tehtubruk2.jpg',
                            ],
                            'Nasi dan Mie' => [
                                'Mie Goreng' => 'miegoreng.png',
                                'Indomie Rebus' => 'indomierebus.png',
                                'Indomie Goreng+toping' => 'indomiegoreng.png',
                                'Nasi Goreng Gemilang' => 'nasigorenggemilang.png',
                                'Nasi Goreng Seafood' => 'nasigorengseafood.png',
                                'Nasi Goreng Ayam' => 'nasigorengayam.png',
                                'Kwetiau Goreng' => 'kwetiau.png',
                                'Kwetiau Rebus' => 'kwetiaurebus.png',
                            ]
                        ];
                        $gambar = $menu->gambar ?? ($customImages[$kategori->nama_kategori][$menu->nama_menu] ?? $defaultGambar);
                    @endphp

                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card menu-card text-center p-2">
                            <img src="{{ asset("assets/img/{$folder}/{$gambar}") }}"
                                 class="card-img-top"
                                 alt="{{ $menu->nama_menu }}"
                                 onerror="this.onerror=null; this.src='{{ asset('assets/img/default.png') }}';">

                            <div class="card-body">
                                <h6 class="menu-title">{{ $menu->nama_menu }}</h6>
                                <p class="menu-text">Rp. {{ number_format($menu->harga, 0, ',', '.') }}</p>
                                <p class="menu-text"><strong>Stok:</strong> {{ $menu->stok }}</p>

                                @if ($menu->stok > 0)
                                    <button class="btn btn-success w-100 add-to-cart-btn"
                                            data-id="{{ $menu->id }}"
                                            data-name="{{ $menu->nama_menu }}"
                                            data-price="{{ $menu->harga }}">
                                        Tambah ke Keranjang
                                    </button>
                                @else
                                    <button class="btn btn-secondary w-100 rounded-pill" disabled>Habis</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
<!-- Booking Section End -->

<!-- SweetAlert2 Notifikasi Meja -->
@if(session('notif_meja') && isset($mejaData))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                icon: 'success',
                title: 'Meja Terdeteksi!',
                html: `
                    <p>Nomor Meja: <strong>{{ $mejaData->nomor_meja }}</strong></p>
                    <p>Tipe Meja: <strong>{{ ucfirst($mejaData->tipe_meja) }}</strong></p>
                    <p>Lantai: <strong>{{ $mejaData->lantai }}</strong></p>
                `,
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'rounded-lg',
                    confirmButton: 'btn btn-success'
                },
                timer: 5000,
                timerProgressBar: true
            });

            // Hapus session notif_meja agar tidak muncul lagi saat reload
            fetch("{{ route('hapus.notif.meja') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            });
        });
    </script>
@endif
@endsection

@section('scripts')
<!-- Load jQuery before addtocart.js -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('assets/js/addtocart.js') }}"></script>
@endsection
