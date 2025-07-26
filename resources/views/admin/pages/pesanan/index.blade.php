    @extends('admin.main')

    @section('header')
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Pesanan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Pesanan</li>
                </ol>
            </div>
        </div>
    @endsection

    @section('content')
        <div class="row">
            <div class="col">
                <div class="card border-primary shadow-sm">

                    {{-- Header: Filter Dropdown dan Tombol Tambah --}}
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                🔍 Filter Jenis Pesanan
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('admin.pesanan.index', ['jenis' => 'dinein']) }}">Dine-In</a>
                                <a class="dropdown-item" href="{{ route('admin.pesanan.index', ['jenis' => 'takeaway']) }}">Takeaway</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-muted" href="{{ route('admin.pesanan.index') }}">Tampilkan Semua</a>
                            </div>
                        </div>

                        <a href="{{ route('admin.pesanan.create') }}" class="btn btn-sm btn-success ml-auto">
                            ➕ Tambah Pesanan
                        </a>
                    </div>

                    {{-- Filter Bar Horizontal --}}
                    <div class="text-center py-2">
                        @if(request('jenis') === 'dinein')
                            <span class="badge badge-success py-2 px-4" style="font-size: 1rem;">Dinein</span>
                        @elseif(request('jenis') === 'takeaway')
                            <span class="badge badge-info py-2 px-4" style="font-size: 1rem;">Takeaway</span>
                        @else
                            <span class="badge badge-secondary py-2 px-4" style="font-size: 1rem;">Semua</span>
                        @endif
                    </div>

                    {{-- Tabel --}}
                    <div class="card-body pt-0">
                        <table class="table table-bordered text-center table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Tanggal Pesanan</th>
                                    <th>Waktu Pesanan</th>
                                    <th>Jenis Pesanan</th>

                                    @if(request('jenis') === 'dinein')
                                        <th>Nomor Meja</th>
                                        <th>Tipe Meja</th>
                                        <th>Lantai</th>
                                    @elseif(request('jenis') === 'takeaway')
                                        <th>Nama Pelanggan</th>
                                        <th>Nomor WhatsApp</th>
                                    @else
                                        <th>Nama Pelanggan</th>
                                        <th>Nomor WhatsApp</th>
                                        <th>Nomor Meja</th>
                                        <th>Tipe Meja</th>
                                        <th>Lantai</th>
                                    @endif

                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pesanans as $pesanan)
                                    <tr>
                                        <td>{{ $pesanan->tanggal_pesanan }}</td>
                                        <td>{{ $pesanan->waktu_pesanan }}</td>
                                        <td>
                                            @php
                                                $jenis = strtolower(str_replace('-', '', $pesanan->jenis_pesanan));
                                            @endphp

                                            @if($jenis === 'dinein')
                                                <span class="badge badge-warning">Dine-In</span>
                                            @elseif($jenis === 'takeaway')
                                                <span class="badge badge-success">Takeaway</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($pesanan->jenis_pesanan) }}</span>
                                            @endif
                                        </td>

                                        @if(request('jenis') === 'dinein')
                                            <td>{{ $pesanan->meja->nomor_meja ?? '-' }}</td>
                                            <td>{{ $pesanan->meja->tipe_meja ?? '-' }}</td>
                                            <td>{{ $pesanan->meja->lantai ?? '-' }}</td>
                                        @elseif(request('jenis') === 'takeaway')
                                            <td>{{ $pesanan->nama_pelanggan ?? '-' }}</td>
                                            <td>{{ $pesanan->nomor_wa ?? '-' }}</td>
                                        @else
                                            <td>{{ $pesanan->nama_pelanggan ?? '-' }}</td>
                                            <td>{{ $pesanan->nomor_wa ?? '-' }}</td>
                                            <td>{{ $pesanan->meja->nomor_meja ?? '-' }}</td>
                                            <td>{{ $pesanan->meja->tipe_meja ?? '-' }}</td>
                                            <td>{{ $pesanan->meja->lantai ?? '-' }}</td>
                                        @endif

                                        <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge
                                                {{ $pesanan->status_pesanan == 'pending' ? 'badge-warning' : '' }}
                                                {{ $pesanan->status_pesanan == 'dibayar' ? 'badge-primary' : '' }}
                                                {{ $pesanan->status_pesanan == 'selesai' ? 'badge-success' : '' }}
                                                {{ $pesanan->status_pesanan == 'dibatalkan' ? 'badge-danger' : '' }}">
                                                {{ ucfirst($pesanan->status_pesanan) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center" style="gap: 6px;">
                                                <a href="{{ route('admin.pesanan.edit', $pesanan->id) }}" class="btn btn-sm btn-warning">
                                                    ✏️ Ubah
                                                </a>
                                                <form action="{{ route('admin.pesanan.destroy', $pesanan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        ❌ Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-muted text-center">Data pesanan tidak tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    @endsection
