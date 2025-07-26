@extends('landing.components.layout')

@section('title', 'Home - Gemilang Cafe')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Hidden input untuk simpan session (dapat diakses via JavaScript) -->
<input type="hidden" id="session-jenis" value="{{ session('jenis_pesanan') }}">
<input type="hidden" id="session-meja" value="{{ session('meja_id') }}">
<input type="hidden" id="session-wa" value="{{ session('takeaway')['nomor_wa'] ?? '' }}">


<!-- Hero Start -->
<div class="container-fluid py-6 my-6 mt-0" style="background-image: url('{{ asset('assets/img/banner/banner-gemilang.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="container text-center animated bounceInDown" style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px;">
        <h1 class="display-1 mb-4">Takeaway</h1>
        <ol class="breadcrumb justify-content-center mb-0 animated bounceInDown">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item text-dark" aria-current="page">Takeaway</li>
        </ol>
    </div>
</div>
<!-- Hero End -->

<!-- Pemanggilan CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/booking.css') }}">

{{-- Rekomendasi Menu --}}
<div class="d-flex flex-column align-items-center mb-3">
    <div class="rekomendasi-frame">
        <a href="{{ route('menu.rekomendasi', ['tipe' => 'takeaway']) }}">
            <i class="fas fa-utensils"></i>
            <span>Rekomendasi Menu</span>
        </a>
    </div>
</div>

<div class="booking-section">
    <div class="container text-center">
        <h1 class="section-title display-4">Gemilang Cafe & Saung</h1>
        <p class="section-subtitle text-center">Pesan menu favorit Anda dengan mudah dan cepat!</p>
    </div>

    @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        });
    </script>
    @endif

    <!-- Elemen untuk menangkap session jenis_pesanan -->
    <div id="jenis-pesanan-session" data-jenis="{{ session('jenis_pesanan') }}"></div>

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
                                'Jeruk panas' => 'jerukpanas.jpg',
                                'Jeruk Dingin' => 'jerukdingin.jpg',
                                'Jeruk Panas' => 'jerukpanas.jpg',
                                'Teh Manis Panas' => 'tehmanis.jpg',
                                'Teh Manis Dingin' => 'tehmanis.jpg',
                                'Coffe Ekspresso' => 'coffekspresso.jpg',
                                'Cappucino Ice' => 'cappucinoice.jpg',
                                'Cappucino Hot' => 'cappucinohot.jpg',
                                'Cofe Susu Gula Aren' => 'coffesusugularen.jpg',
                                'Best Latte Ice' => 'bestlattehot.jpg',
                                'Cofe Latte Ice' => 'coffelatteice.jpg',
                                'Cofe Latte Hot' => 'coffelatehot.jpg',
                                'Best Latte Hot' => 'bestlattehot.jpg',
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
@endsection

@section('scripts')
<!-- Load jQuery sebelum addtocart.js -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('assets/js/addtocart.js') }}"></script>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    });
</script>
@endif

@if (session('jenis_pesanan') == 'takeaway')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'info',
            title: 'Takeaway Aktif!',
            text: 'Anda sedang melakukan pemesanan takeaway.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    });
</script>
@endif
@endsection
