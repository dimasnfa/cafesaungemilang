@extends('admin.main')

@section('content')
<style>
    table thead th {
        background: linear-gradient(to right, #007bff, #0056b3);
        color: white;
        text-align: center;
    }

    table td, table th {
        vertical-align: middle !important;
        padding: 10px 14px !important;
        font-size: 14px;
    }

    .filter-container {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-container label {
        font-weight: 600;
        margin-right: 10px;
    }

    .filter-container select,
    .filter-container input {
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid #ced4da;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .btn-export {
        margin-left: 10px;
    }
</style>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📊 Laporan Transaksi</h5>
    </div>

    <div class="card-body">

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="filter-container">
            <label>Filter:</label>
            <select name="filter_type" onchange="this.form.submit()">
                <option value="harian" {{ $filterType === 'harian' ? 'selected' : '' }}>Harian</option>
                <option value="bulanan" {{ $filterType === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                <option value="tahunan" {{ $filterType === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
            </select>

            @if ($filterType === 'harian')
                <input type="date" name="harian" value="{{ $tanggal }}" onchange="this.form.submit()">
            @elseif ($filterType === 'bulanan')
                <input type="month" name="bulanan" value="{{ $bulan }}" onchange="this.form.submit()">
            @elseif ($filterType === 'tahunan')
                <input type="number" name="tahunan" value="{{ $tahun }}" min="2000" max="{{ date('Y') }}" onchange="this.form.submit()" placeholder="Tahun">
            @endif
        </form>

        {{-- Table --}}
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover table-striped text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Transaksi</th>
                        <th>Jenis Pesanan</th>
                        <th>Menu</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporans as $laporan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($laporan->pesanan->tanggal_pesanan)->format('d-m-Y') }}</td>
                            <td class="text-capitalize">
                                {{ $laporan->pesanan->jenis_pesanan ?? ($laporan->pesanan->meja_id ? 'dinein' : 'takeaway') }}
                            </td>
                            <td>{{ $laporan->menu->nama_menu }}</td>
                            <td>{{ $laporan->menu->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $laporan->jumlah }}</td>
                            <td>Rp {{ number_format($laporan->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada data laporan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Total Pendapatan --}}
        @if($laporans->count())
            <div class="mt-3 text-end">
                <h5>
                    Total Pendapatan: 
                    <span class="badge bg-success">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </span>
                </h5>
            </div>
        @endif

        {{-- Export Buttons --}}
        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('admin.laporan.export-pdf', request()->query()) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>
            <a href="{{ route('admin.laporan.export-csv', request()->query()) }}" class="btn btn-success fw-bold shadow-sm btn-export">
                <i class="fas fa-file-csv"></i> Download CSV
            </a>
        </div>
    </div>
</div>
@endsection
