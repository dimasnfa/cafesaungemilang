@extends('admin.main')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Detail Pesanan</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.pesanan.index') }}">Pesanan</a></li>
                <li class="breadcrumb-item active">Detail Pesanan</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Informasi Pesanan</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Nama Pelanggan:</strong> {{ $pesanan->user->name }}
                </div>
                <div class="mb-2">
                    <strong>Tanggal:</strong> {{ $pesanan->tanggal_pesanan }}
                </div>
                <div class="mb-2">
                    <strong>Waktu:</strong> {{ $pesanan->waktu_pesanan }}
                </div>
                <div class="mb-2">
                    <strong>Jenis:</strong> {{ $pesanan->jenis_pesanan ?? '-' }}
                </div>
                <div class="mb-2">
                    <strong>Total:</strong> Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                </div>
                <div class="mb-2">
                    <strong>Status:</strong>
                    <span class="badge
                        {{ $pesanan->status_pesanan == 'pending' ? 'badge-warning' : '' }}
                        {{ $pesanan->status_pesanan == 'dibayar' ? 'badge-primary' : '' }}
                        {{ $pesanan->status_pesanan == 'selesai' ? 'badge-success' : '' }}
                        {{ $pesanan->status_pesanan == 'dibatalkan' ? 'badge-danger' : '' }}">
                        {{ ucfirst($pesanan->status_pesanan) }}
                    </span>
                </div>

                @if ($pesanan->menu && $pesanan->menu->count())
                    <hr>
                    <h6>Detail Menu Dipesan:</h6>
                    <ul>
                        @foreach ($pesanan->menu as $menu)
                            <li>{{ $menu->nama }} x {{ $menu->pivot->jumlah }} — Rp {{ number_format($menu->harga * $menu->pivot->jumlah, 0, ',', '.') }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.pesanan.index') }}" class="btn btn-sm btn-outline-secondary">⬅️ Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
