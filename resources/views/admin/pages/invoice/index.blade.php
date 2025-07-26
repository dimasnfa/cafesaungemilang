@extends('admin.main')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Kelola Pembayaran</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Pembayaran</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Daftar Pembayaran</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <table class="table table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>ID Pesanan</th>
                                <th>Nama Pelanggan</th>
                                <th>Total Bayar</th>
                                <th>Metode Pembayaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pembayarans as $pembayaran)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>PESANAN-{{ $pembayaran->pesanan_id ?? 'N/A' }}</td>
                                    <td>{{ $pembayaran->user ? $pembayaran->user->name : 'Tidak Diketahui' }}</td>
                                    <td>Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst($pembayaran->metode_pembayaran ?? 'Tidak Ada') }}</td>
                                    <td>
                                        <span class="badge 
                                            {{ $pembayaran->status_pembayaran === 'dibayar' ? 'badge-success' : 
                                            ($pembayaran->status_pembayaran === 'dibatalkan' ? 'badge-danger' : 'badge-warning') }}">
                                            {{ ucfirst($pembayaran->status_pembayaran) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.pembayaran.edit', $pembayaran->id) }}" class="btn btn-sm btn-warning">✏️ Ubah</a>
                                        <form action="{{ route('admin.pembayaran.destroy', $pembayaran->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pembayaran ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">❌ Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada pembayaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Tambahkan Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $pembayarans->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
