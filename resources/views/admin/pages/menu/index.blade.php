@extends('admin.main')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1></h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Menu</li>
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
                {{-- Card Header dengan tombol di pojok kanan --}}
                <div class="card-header bg-primary text-white">
                    <div class="row w-100 align-items-center">
                        <div class="col-md-6">
                            <h3 class="card-title mb-0">Daftar Menu</h3>
                        </div>
                       <div class="col-md-6 d-flex justify-content-end">

                            <a href="{{ route('admin.menu.create') }}" class="btn btn-warning btn-sm fw-bold shadow-sm">
                                ➕ Tambah Menu
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    {{-- Tabel Menu dengan scroll horizontal --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Menu</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($menus as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ ucfirst($item->nama_menu) }}</td>
                                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td>{{ $item->stok }}</td>
                                        <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center" style="gap: 15px;">
                                                <a href="{{ route('admin.menu.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                                    ✏️ Ubah
                                                </a>
                                                <form action="{{ route('admin.menu.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        ❌ Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Data menu tidak tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
