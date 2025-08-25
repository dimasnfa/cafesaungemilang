@extends('admin.main')

@section('title', 'Edit Pesanan')

@section('content')
<div class="container mt-4">
    <h2>Edit Pesanan</h2>

    @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.pesanan.update', $pesanan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="status_pesanan" class="form-label">Status Pesanan</label>
            <select name="status_pesanan" id="status_pesanan" class="form-control" required>
                <option value="pending" {{ $pesanan->status_pesanan == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="dibayar" {{ $pesanan->status_pesanan == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                {{-- <option value="selesai" {{ $pesanan->status_pesanan == 'selesai' ? 'selected' : '' }}>Selesai</option> --}}
                <option value="dibatalkan" {{ $pesanan->status_pesanan == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>

    <hr>

    <h4>Detail Pesanan</h4>
    <ul class="list-group">
        @forelse ($pesanan->detailPesanan as $item)
            @if ($item->menu)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $item->menu->nama_menu }}
                    <span class="badge bg-primary rounded-pill">x{{ $item->jumlah }}</span>
                </li>
            @endif
        @empty
            <li class="list-group-item">Tidak ada detail pesanan.</li>
        @endforelse
    </ul>

    <div class="mt-3">
        <strong>Total Harga:</strong> Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}
    </div>

    @if($pesanan->meja)
        <div class="mt-2">
            <strong>Meja:</strong> {{ $pesanan->meja->nomor_meja }} - {{ $pesanan->meja->tipe }} (Lantai {{ $pesanan->meja->lantai }})
        </div>
    @else
        <div class="mt-2">
            <strong>Nama Pelanggan:</strong> {{ $pesanan->nama_pelanggan }}<br>
            <strong>Nomor WA:</strong> {{ $pesanan->nomor_wa }}
        </div>
    @endif
</div>
@endsection
