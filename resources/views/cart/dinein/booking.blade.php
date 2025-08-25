@extends('landing.components.layout')

@section('title', 'Dine-in - Gemilang Cafe')

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

<!-- CSS untuk Tab System dan Kategori -->
<style>
/* Tab Navigation Styling */
.menu-tabs {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.nav-tabs {
    border: none;
    justify-content: center;
    flex-wrap: wrap;
}

.nav-tabs .nav-item {
    margin: 5px;
}

.nav-tabs .nav-link {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: white;
    border-radius: 50px;
    padding: 12px 25px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.nav-tabs .nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.nav-tabs .nav-link.active {
    background: linear-gradient(45deg, #28a745, #20c997);
    border-color: #28a745;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

/* Tab Content */
.tab-content {
    min-height: 500px;
}

.tab-pane {
    display: none;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.4s ease;
}

.tab-pane.active.show {
    display: block;
    opacity: 1;
    transform: translateY(0);
}

/* Menu Cards Enhancement */
.menu-card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    height: 100%;
}

.menu-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.menu-card .card-img-top {
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.menu-card:hover .card-img-top {
    transform: scale(1.05);
}

.menu-card .card-body {
    padding: 20px;
    text-align: center;
}

.menu-title {
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
    font-size: 16px;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.menu-text {
    color: #666;
    margin-bottom: 8px;
}

.price-tag {
    background: linear-gradient(45deg, #ff6b6b, #ee5a52);
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: bold;
    margin-bottom: 15px;
    display: inline-block;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
}

.stock-info {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 13px;
    margin-bottom: 15px;
    display: inline-block;
    font-weight: 600;
}

.stock-info.out-of-stock {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.add-to-cart-btn {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    border-radius: 25px;
    padding: 12px 20px;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 13px;
}

.add-to-cart-btn:hover {
    background: linear-gradient(45deg, #20c997, #17a2b8);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
    color: white;
}

/* Category Header */
.category-header {
    text-align: center;
    margin: 40px 0 30px 0;
    position: relative;
}

.category-header h3 {
    background: linear-gradient(45deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: bold;
    font-size: 2rem;
    margin: 0;
    position: relative;
}

.category-header::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    border-radius: 2px;
}

/* Empty State */
.empty-category {
    text-align: center;
    padding: 80px 20px;
    color: #6c757d;
}

.empty-category i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-category h4 {
    margin-bottom: 10px;
    color: #495057;
}

.empty-category p {
    font-size: 14px;
    opacity: 0.8;
}

/* Loading Animation */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Recommendation Popup Enhancement */
.recommendation-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
    margin-bottom: 15px;
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
}

.recommendation-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.recommendation-img {
    height: 120px;
    object-fit: cover;
    border-radius: 10px;
}

.recommendation-btn {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    border-radius: 20px;
    color: white;
    font-weight: 600;
    padding: 8px 16px;
    transition: all 0.3s ease;
}

.recommendation-btn:hover {
    background: linear-gradient(45deg, #20c997, #17a2b8);
    transform: scale(1.05);
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 10px 15px;
        font-size: 12px;
        margin: 3px;
    }
    
    .menu-card .card-img-top {
        height: 150px;
    }
    
    .menu-title {
        font-size: 14px;
        min-height: 35px;
    }
    
    .category-header h3 {
        font-size: 1.5rem;
    }
    
    .recommendation-img {
        height: 100px;
    }
}

@media (max-width: 480px) {
    .nav-tabs .nav-link {
        padding: 8px 12px;
        font-size: 11px;
    }
    
    .menu-card .card-img-top {
        height: 120px;
    }
    
    .menu-title {
        font-size: 13px;
        min-height: 30px;
    }
    
    .category-header h3 {
        font-size: 1.3rem;
    }
    
    .recommendation-img {
        height: 80px;
    }
}

/* Smooth scroll for navigation links */
html {
    scroll-behavior: smooth;
}
</style>

{{-- Info Meja yang Dipilih --}}
@if(isset($mejaData) && session('jenis_pesanan') === 'dinein')
<div class="container mb-4">
    <div class="alert alert-info text-center" role="alert">
        <h5 class="mb-2">
            <i class="fas fa-table me-2"></i>
            Meja yang Dipilih
        </h5>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <p class="mb-1"><strong>Nomor Meja:</strong> {{ $mejaData->nomor_meja }}</p>
                <p class="mb-1"><strong>Tipe:</strong> {{ ucfirst($mejaData->tipe_meja) }}</p>
                <p class="mb-0"><strong>Lantai:</strong> {{ $mejaData->lantai }}</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Booking Section Start -->
<div class="booking-section">
    <div class="container text-center">
        <h1 class="section-title display-4">Gemilang Cafe & Saung</h1>
        <p class="section-subtitle">Pesan menu favorit Anda dengan mudah dan cepat!</p>
    </div>

    <div class="container">
        <h2 class="text-center text-dark fw-bold mb-4">Daftar Menu</h2>

        <!-- Tab Navigation -->
        <div class="menu-tabs">
            <ul class="nav nav-tabs" id="menuTabs" role="tablist">
                @foreach ($kategoris as $index => $kategori)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                id="{{ Str::slug($kategori->nama_kategori) }}-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#{{ Str::slug($kategori->nama_kategori) }}" 
                                type="button" 
                                role="tab" 
                                aria-controls="{{ Str::slug($kategori->nama_kategori) }}" 
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            <i class="fas fa-{{ 
                                $kategori->nama_kategori === 'Makanan' ? 'utensils' : 
                                ($kategori->nama_kategori === 'Minuman' ? 'coffee' : 
                                ($kategori->nama_kategori === 'Nasi dan Mie' ? 'bowl-rice' : 
                                ($kategori->nama_kategori === 'Aneka Snack' ? 'cookie-bite' : 'list'))) 
                            }} me-2"></i>
                            {{ $kategori->nama_kategori }}
                            <span class="badge bg-light text-dark ms-2">{{ $kategori->menus->count() }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="menuTabsContent">
            @foreach ($kategoris as $index => $kategori)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                     id="{{ Str::slug($kategori->nama_kategori) }}" 
                     role="tabpanel" 
                     aria-labelledby="{{ Str::slug($kategori->nama_kategori) }}-tab">
                    
                    <div class="category-header">
                        <h3>{{ $kategori->nama_kategori }}</h3>
                    </div>

                    @if($kategori->menus->count() > 0)
                        <div class="row" data-kategori="{{ $kategori->nama_kategori }}">
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
                                            'Ayam Rica Rica' => 'ayamricarica.jpg',
                                            'Sop Ayam' => 'sopayam.jpg',
                                            'Garang Asem Ayam' => 'garangasem.jpg',
                                            'Sambal Mentah' => 'sambalmentah.jpg',
                                            'Sambal Pecak' => 'sambalpecak.jpg',
                                            'Sambal Terasi' => 'sambalterasi.jpg',
                                            'Sambal Geprek' => 'sambalgeprek.jpg',
                                            'Sambal Bawang' => 'sambalbawang.jpg',
                                            'Sambal Ijo' => 'sambalijo.jpg',
                                            'Sambal Dabu Dabu' => 'sambaldabu.jpg',
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
                                            'Indomie Goreng toping' => 'indomiegoreng.png',
                                            'Nasi Goreng Gemilang' => 'nasigorenggemilang.png',
                                            'Nasi Goreng Seafood' => 'nasigorengseafood.png',
                                            'Nasi Goreng Ayam' => 'nasigorengayam.png',
                                            'Kwetiau Goreng' => 'kwetiau.png',
                                            'Kwetiau Rebus' => 'kwetiaurebus.png',
                                        ],
                                       'Aneka Snack' => [
                                            'French Fries' => 'frenchfries.jpg',
                                            'Keong Racun' => 'keongracun.jpg',
                                            'Kongkou Snack' => 'KongkouSnack.jpg',
                                            'Nugget' => 'nugget.jpg',
                                            'Pisang Bakar' => 'pisangbakar.jpg',
                                            'Roti Bakar' => 'rotibakar.png',
                                            'Roti Bakar Keju Coklat' => 'rotibakarkejucoklat.jpg',
                                            'Sosis Goreng' => 'sosisgoreng.jpg',
                                            'Tahu Tepung' => 'tahutepung.jpg',
                                        ],
                                    ];
                                    $gambar = $menu->gambar ?? ($customImages[$kategori->nama_kategori][$menu->nama_menu] ?? $defaultGambar);
                                @endphp

                                <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-menu-id="{{ $menu->id }}" data-kategori="{{ $kategori->nama_kategori }}">
                                    <div class="card menu-card text-center">
                                        <img src="{{ asset("assets/img/{$folder}/{$gambar}") }}"
                                             class="card-img-top"
                                             alt="{{ $menu->nama_menu }}"
                                             onerror="this.onerror=null; this.src='{{ asset('assets/img/default.png') }}';">

                                        <div class="card-body">
                                            <h6 class="menu-title">{{ $menu->nama_menu }}</h6>
                                            
                                            <div class="price-tag">
                                                Rp. {{ number_format($menu->harga, 0, ',', '.') }}
                                            </div>
                                            
                                            <div class="stock-info {{ $menu->stok <= 0 ? 'out-of-stock' : '' }}">
                                                <i class="fas fa-{{ $menu->stok > 0 ? 'check-circle' : 'times-circle' }} me-1"></i>
                                                {{ $menu->stok > 0 ? 'Tersedia ('.$menu->stok.')' : 'Habis' }}
                                            </div>

                                            @if ($menu->stok > 0)
                                                <button class="btn add-to-cart-btn w-100"
                                                        data-id="{{ $menu->id }}"
                                                        data-name="{{ $menu->nama_menu }}"
                                                        data-price="{{ $menu->harga }}"
                                                        data-kategori="{{ $kategori->nama_kategori }}">
                                                    <i class="fas fa-cart-plus me-2"></i>
                                                    Tambah ke Keranjang
                                                </button>
                                            @else
                                                <button class="btn btn-secondary w-100" disabled>
                                                    <i class="fas fa-ban me-2"></i>
                                                    Tidak Tersedia
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-category">
                            <i class="fas fa-utensils"></i>
                            <h4>Menu Tidak Tersedia</h4>
                            <p>Maaf, kategori ini belum memiliki menu yang tersedia saat ini.</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Booking Section End -->

<!-- Include Komponen Rekomendasi Menu -->
@include('cart.dinein.rekomendasimenu')

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap 5 JS (jika belum ada) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Notifikasi Meja Berhasil Scan QR -->
@if(isset($showNotifikasi) && $showNotifikasi && isset($mejaData))
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Show notification popup
    Swal.fire({
        icon: 'success',
        title: 'QR Code Berhasil di Scan!',
        html: `
            <div style="text-align: center; padding: 10px;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                           color: white; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                    <h4 style="margin: 0; color: white;">
                        <i class="fas fa-table" style="margin-right: 8px;"></i>
                        Detail Meja
                    </h4>
                </div>
                <div style="text-align: left; max-width: 250px; margin: 0 auto;">
                    <p style="margin: 8px 0; font-size: 16px;">
                        <strong>Nomor Meja:</strong> 
                        <span style="color: #2e7d32; font-weight: bold;">{{ $mejaData->nomor_meja }}</span>
                    </p>
                    <p style="margin: 8px 0; font-size: 16px;">
                        <strong>Tipe Meja:</strong> 
                        <span style="color: #1976d2; font-weight: bold;">{{ ucfirst($mejaData->tipe_meja) }}</span>
                    </p>
                    <p style="margin: 8px 0; font-size: 16px;">
                        <strong>Lantai:</strong> 
                        <span style="color: #f57c00; font-weight: bold;">{{ $mejaData->lantai }}</span>
                    </p>
                    <p style="margin: 8px 0; font-size: 16px;">
                        <strong>Status:</strong> 
                        <span style="color: #388e3c; font-weight: bold;">{{ ucfirst($mejaData->status) }}</span>
                    </p>
                </div>
                <div style="margin-top: 15px; padding: 10px; background-color: #e8f5e8; 
                           border-radius: 8px; border-left: 4px solid #4caf50;">
                    <p style="margin: 0; color: #2e7d32; font-weight: 500;">
                        🍽 Silakan pilih menu favorit Anda!
                    </p>
                </div>
            </div>
        `,
        confirmButtonText: '🛒 Mulai Pesan',
        confirmButtonColor: '#4caf50',
        width: '450px',
        customClass: {
            popup: 'rounded-lg shadow-lg',
            title: 'font-weight-bold',
            confirmButton: 'btn btn-success btn-lg px-4'
        },
        showClass: {
            popup: 'animate_animated animate_bounceIn'
        },
        hideClass: {
            popup: 'animate_animated animate_bounceOut'
        },
        timer: 10000,
        timerProgressBar: true,
        allowOutsideClick: true,
        allowEscapeKey: true
    }).then((result) => {
        if (result.isConfirmed || result.isDismissed) {
            const menuSection = document.querySelector('.booking-section');
            if (menuSection) {
                menuSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });

    // Clear notification session via AJAX
    fetch("{{ route('hapus.notif.meja') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    }).catch(error => {
        console.log('Failed to clear notification session:', error);
    });
});
</script>
@endif

<!-- Success/Error Messages from Session -->
@if(session('success'))
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#4caf50',
        timer: 3000,
        timerProgressBar: true
    });
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#f44336',
        timer: 5000,
        timerProgressBar: true
    });
});
</script>
@endif

<!-- ✅ Script utama untuk add to cart dengan popup rekomendasi -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // ✅ Function untuk update cart count di navbar
    function updateCartCountInNavbar(cartCount) {
        const cartCountElement = document.getElementById('cart-count-dinein');
        if (cartCountElement) {
            const currentCount = parseInt(cartCountElement.textContent) || 0;
            
            if (currentCount !== cartCount) {
                cartCountElement.textContent = cartCount;
                
                // ✅ Animasi enhanced untuk memberikan feedback visual
                cartCountElement.classList.add('cart-badge-animate');
                const cartIcon = cartCountElement.parentElement.querySelector('.cart-icon');
                if (cartIcon) {
                    cartIcon.classList.add('cart-icon-bounce', 'cart-icon-highlight');
                }
                
                setTimeout(() => {
                    cartCountElement.classList.remove('cart-badge-animate');
                    if (cartIcon) {
                        cartIcon.classList.remove('cart-icon-bounce', 'cart-icon-highlight');
                    }
                }, 1000);
                
                console.log('Cart count updated to:', cartCount);
            }
        }
    }

    // ✅ Function untuk mendapatkan rekomendasi berdasarkan kategori
    function getMenuRecommendations(selectedMenuId, selectedKategori) {
        const allMenus = document.querySelectorAll('[data-menu-id]');
        const recommendations = [];
        
        allMenus.forEach(menuElement => {
            const menuId = menuElement.getAttribute('data-menu-id');
            const kategori = menuElement.getAttribute('data-kategori');
            
            // Ambil menu dari kategori yang sama, kecuali menu yang baru ditambahkan
            if (kategori === selectedKategori && menuId !== selectedMenuId) {
                const menuCard = menuElement.querySelector('.menu-card');
                const menuImg = menuCard.querySelector('img').src;
                const menuTitle = menuCard.querySelector('.menu-title').textContent;
                const menuPriceTag = menuCard.querySelector('.price-tag').textContent;
                const addBtn = menuCard.querySelector('.add-to-cart-btn');
                
                if (addBtn && !addBtn.disabled) {
                    recommendations.push({
                        id: menuId,
                        name: menuTitle,
                        price: addBtn.getAttribute('data-price'),
                        image: menuImg,
                        priceFormatted: menuPriceTag
                    });
                }
            }
        });
        
        // Shuffle dan ambil maksimal 3 rekomendasi
        return recommendations.sort(() => 0.5 - Math.random()).slice(0, 3);
    }

    // ✅ Function untuk menampilkan popup rekomendasi
    function showRecommendationPopup(selectedMenuId, selectedKategori, addedMenuName) {
        const recommendations = getMenuRecommendations(selectedMenuId, selectedKategori);
        
        if (recommendations.length === 0) return;
        
        let recommendationHTML = `
            <div style="text-align: center; padding: 15px;">
                <div style="background: linear-gradient(135deg, #ff6b6b, #4ecdc4); 
                           color: white; padding: 15px; border-radius: 15px; margin-bottom: 20px;">
                    <h4 style="margin: 0; color: white;">
                        <i class="fas fa-heart" style="margin-right: 8px;"></i>
                        Rekomendasi Menu Lainnya
                    </h4>
                    <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">
                        Menu dari kategori yang sama dengan "${addedMenuName}"
                    </p>
                </div>
                <div class="row">
        `;
        
        recommendations.forEach(menu => {
            recommendationHTML += `
                <div class="col-12 mb-3">
                    <div class="recommendation-card card p-3">
                        <div class="row align-items-center">
                            <div class="col-4">
                                <img src="${menu.image}" class="recommendation-img w-100" alt="${menu.name}">
                            </div>
                            <div class="col-5 text-start">
                                <h6 class="mb-1 fw-bold" style="font-size: 14px;">${menu.name}</h6>
                                <p class="mb-0 text-success fw-bold" style="font-size: 13px;">${menu.priceFormatted}</p>
                            </div>
                            <div class="col-3">
                                <button class="recommendation-btn btn btn-sm w-100" 
                                        data-rec-id="${menu.id}" 
                                        data-rec-name="${menu.name}" 
                                        data-rec-price="${menu.price}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        recommendationHTML += `
             
        `;
        
        Swal.fire({
            title: '🍽 Menu Telah Ditambahkan!',
            html: recommendationHTML,
            width: '500px',
            // showCancelButton: true,
            // cancelButtonText: '✖ Tutup',
            // confirmButtonColor: '#28a745',
            // cancelButtonColor: '#6c757d',
            customClass: {
                popup: 'rounded-lg shadow-xl',
                title: 'fw-bold',
                confirmButton: 'btn btn-success px-4 py-2',
                cancelButton: 'btn btn-secondary px-4 py-2'
            },
            showClass: {
                popup: 'animate_animated animate_bounceIn'
            },
            hideClass: {
                popup: 'animate_animated animate_fadeOut'
            },
            timer: 15000,
            timerProgressBar: true,
            allowOutsideClick: true,
            allowEscapeKey: true,
            didOpen: () => {
                // ✅ Handle klik tombol rekomendasi
                document.querySelectorAll('.recommendation-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const recMenuId = this.getAttribute('data-rec-id');
                        const recMenuName = this.getAttribute('data-rec-name');
                        const recMenuPrice = this.getAttribute('data-rec-price');
                        
                        addRecommendedToCart(recMenuId, recMenuName, recMenuPrice);
                    });
                });
            }
        });
    }

    // ✅ Function untuk menambahkan rekomendasi ke keranjang
    function addRecommendedToCart(menuId, menuName, menuPrice) {
        const jenisPesanan = document.getElementById('session-jenis').value;
        const mejaId = document.getElementById('session-meja').value;
        
        // Update button state
        const recBtn = document.querySelector(`[data-rec-id="${menuId}"]`);
        const originalHTML = recBtn.innerHTML;
        recBtn.disabled = true;
        recBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        
        fetch('/dinein/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                menu_id: menuId,
                jenis_pesanan: 'dinein',
                meja_id: mejaId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count
                if (data.cart_count !== undefined) {
                    updateCartCountInNavbar(data.cart_count);
                }
                
                // Show mini success message
                recBtn.innerHTML = '<i class="fas fa-check text-white"></i>';
                recBtn.style.background = 'linear-gradient(45deg, #28a745, #20c997)';
                
                // Show toast notification
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
                
                Toast.fire({
                    icon: 'success',
                    title: `${menuName} ditambahkan!`
                });
                
                setTimeout(() => {
                    recBtn.disabled = true;
                    recBtn.innerHTML = '<i class="fas fa-check"></i> Ditambahkan';
                    recBtn.style.background = 'linear-gradient(45deg, #6c757d, #adb5bd)';
                }, 1500);
                
            } else {
                recBtn.disabled = false;
                recBtn.innerHTML = originalHTML;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Gagal menambahkan menu rekomendasi',
                    confirmButtonColor: '#f44336',
                    timer: 3000
                });
            }
        })
        .catch(error => {
            console.error('Error adding recommended menu:', error);
            recBtn.disabled = false;
            recBtn.innerHTML = originalHTML;
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan sistem',
                confirmButtonColor: '#f44336',
                timer: 3000
            });
        });
    }

    // ✅ Enhanced add to cart dengan popup rekomendasi
    document.querySelectorAll('.add-to-cart-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const menuId = this.getAttribute('data-id');
            const menuName = this.getAttribute('data-name');
            const menuPrice = parseFloat(this.getAttribute('data-price'));
            const menuKategori = this.getAttribute('data-kategori');
            
            // Validasi session data
            const jenisPesanan = document.getElementById('session-jenis').value;
            const mejaId = document.getElementById('session-meja').value;
            
            if (!jenisPesanan || !mejaId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap',
                    text: 'Silakan pilih meja terlebih dahulu',
                    confirmButtonColor: '#f44336'
                });
                return;
            }
            
            // Disable button dan show loading
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menambahkan...';
            
            // ✅ AJAX request ke endpoint yang sudah ada
            fetch('/dinein/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    menu_id: menuId,
                    jenis_pesanan: 'dinein',
                    meja_id: mejaId
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Add to cart response:', data);
                
                if (data.success) {
                    // ✅ Update cart count di navbar langsung dari response
                    if (data.cart_count !== undefined) {
                        updateCartCountInNavbar(data.cart_count);
                    }
                    
                    // Show success message singkat
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: `${menuName} ditambahkan ke keranjang!`
                    }).then(() => {
                        // ✅ Show popup rekomendasi setelah sukses menambah
                        setTimeout(() => {
                            showRecommendationPopup(menuId, menuKategori, menuName);
                        }, 300);
                    });
                    
                    // ✅ Refresh cart count lagi untuk memastikan sinkronisasi
                    if (typeof window.refreshCartCount === 'function') {
                        setTimeout(() => {
                            window.refreshCartCount();
                        }, 500);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Gagal menambahkan item ke keranjang',
                        confirmButtonColor: '#f44336'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem',
                    confirmButtonColor: '#f44336'
                });
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    });
    
    // ✅ Auto-refresh cart count setiap 10 detik untuk memastikan sinkronisasi
    setInterval(() => {
        if (typeof window.refreshCartCount === 'function') {
            window.refreshCartCount();
        }
    }, 10000);
    
    // ✅ Global function untuk compatibility dengan rekomendasi menu lainnya
    window.showRecommendationPopup = showRecommendationPopup;
    window.addRecommendedToCart = addRecommendedToCart;
});

// ✅ Override function untuk rekomendasi menu agar juga update cart count
if (typeof window.addRecommendedToCart !== 'undefined') {
    const originalAddRecommended = window.addRecommendedToCart;
    window.addRecommendedToCart = function(menuId, menuName, menuPrice) {
        // Panggil function asli
        originalAddRecommended(menuId, menuName, menuPrice);
        
        // Refresh cart count setelah menambah rekomendasi
        setTimeout(() => {
            if (typeof window.refreshCartCount === 'function') {
                window.refreshCartCount();
            }
        }, 500);
    };
}
</script>

@endsection

@section('scripts')
<!-- Load jQuery before addtocart.js -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Modified addtocart.js untuk compatibility -->
<script src="{{ asset('assets/js/addtocart.js') }}"></script>
@endsection