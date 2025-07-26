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
    <style>
        table.table th,
        table.table td {
            padding: 12px 10px !important;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .badge {
            font-size: 0.9rem;
            padding: 5px 10px;
        }
    </style>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title mb-0">Daftar Pembayaran</h3>
                        <a href="{{ route('admin.pembayaran.create') }}" class="btn btn-warning btn-sm fw-bold shadow-sm">
                            + Tambah Pembayaran
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    {{-- Filter Jenis Pesanan --}}
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                🔍 Filter Jenis Pesanan
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('admin.pembayaran.index', ['jenis' => 'dinein']) }}">Dine-In</a>
                                <a class="dropdown-item" href="{{ route('admin.pembayaran.index', ['jenis' => 'takeaway']) }}">Takeaway</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-muted" href="{{ route('admin.pembayaran.index') }}">Tampilkan Semua</a>
                            </div>
                        </div>

                        {{-- Filter Badge --}}
                        <div>
                            @if(request('jenis') === 'dinein')
                                <span class="badge bg-success px-3 py-2">Dine-In</span>
                            @elseif(request('jenis') === 'takeaway')
                                <span class="badge bg-info px-3 py-2">Takeaway</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">Semua</span>
                            @endif
                        </div>
                    </div>

                    {{-- Tabel Pembayaran --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>ID Pesanan</th>
                                    <th>Order ID</th>
                                    <th>Jenis Pesanan</th>
                                    @if(!request('jenis') || request('jenis') !== 'takeaway')
                                        <th>Nomor Meja</th>
                                    @endif
                                    <th>Nama Pelanggan</th>
                                    @if(!request('jenis') || request('jenis') !== 'dinein')
                                        <th>Nomor WA</th>
                                    @endif
                                    <th>Total Bayar</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                    <th>Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pembayarans as $pembayaran)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pembayaran->id }}</td>
                                        <td>
                                            @if ($pembayaran->pesanan_id)
                                                PESANAN-{{ $pembayaran->pesanan_id }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $pembayaran->order_id }}</td>
                                        <td>{{ ucfirst($pembayaran->jenis_pesanan) }}</td>
                                        @if(!request('jenis') || request('jenis') !== 'takeaway')
                                            <td>{{ $pembayaran->nomor_meja ?? '-' }}</td>
                                        @endif
                                        <td>{{ $pembayaran->nama_pelanggan ?? '-' }}</td>
                                        @if(!request('jenis') || request('jenis') !== 'dinein')
                                            <td>{{ $pembayaran->nomor_wa ?? '-' }}</td>
                                        @endif
                                        <td>Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}</td>
                                        <td>{{ ucfirst($pembayaran->metode_pembayaran) }}</td>
                                        <td>
                                            <span class="badge 
                                                {{ $pembayaran->status_pembayaran === 'dibayar' ? 'bg-success' :
                                                   ($pembayaran->status_pembayaran === 'gagal' || $pembayaran->status_pembayaran === 'expired' ? 'bg-danger' : 'bg-warning') }}">
                                                {{ ucfirst($pembayaran->status_pembayaran) }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_pesanan)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pembayaran->waktu_pesanan)->format('H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('admin.pembayaran.edit', $pembayaran->id) }}" class="btn btn-sm btn-warning">✏️</a>
                                            <form action="{{ route('admin.pembayaran.destroy', $pembayaran->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pembayaran ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.pembayaran.invoice', $pembayaran->id) }}" class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-file-invoice"></i> Invoice
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="text-center">Belum ada pembayaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-3">
                        {{ $pembayarans->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
