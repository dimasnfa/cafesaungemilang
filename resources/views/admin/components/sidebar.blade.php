@php
    $menus = [
        (object) ["title" => "Dashboard", "path" => "admin/dashboard", "icon" => "fas fa-tachometer-alt"],
        (object) ["title" => "Kategori", "path" => "admin/kategori", "icon" => "fas fa-tasks"],
        (object) ["title" => "Menu", "path" => "admin/menu", "icon" => "fas fa-bars"],
        (object) ["title" => "Meja", "path" => "admin/meja", "icon" => "fas fa-chair"],
        (object) ["title" => "Pesanan", "path" => "admin/pesanan", "icon" => "fas fa-shopping-cart"],
        (object) ["title" => "Detail Pesanan", "path" => "admin/detailpesanan", "icon" => "fas fa-sort"],
        (object) ["title" => "Laporan", "path" => "admin/laporan", "icon" => "fas fa-book"],
        (object) ["title" => "Pembayaran", "path" => "admin/pembayaran", "icon" => "fas fa-credit-card"],
    ];
@endphp

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="{{ asset('assets/icon-gemilang.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Cafe Saung Gemilang</span>
    </a>

    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/admin/profile.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Search -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @foreach($menus as $menu)
                    @if(auth()->user()->hasRole('admin') || 
                (auth()->user()->hasRole('kasir') && in_array($menu->title, ['Meja', 'Menu','Detail Pesanan','Pesanan', 'Pembayaran'])))
                        <li class="nav-item">
                            <a href="{{ $menu->path[0] !== '/' ? '/' . $menu->path : $menu->path }}" class="nav-link {{ request()->is(ltrim($menu->path, '/')) ? 'active' : '' }}">
                                <i class="nav-icon {{ $menu->icon }}"></i>
                                <p>{{ $menu->title }}</p>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
</aside>
