@extends('admin.main')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Kategori</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Kategori</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header d-flex justify-content-end">
                    <a href="{{ route('admin.kategori.create') }}" class="btn btn-sm btn-primary">
                        + Tambah Kategori
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kategori as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ ucfirst($item->nama_kategori) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center" style="gap: 15px;">
                                            <a href="{{ route('admin.kategori.edit', $item->id) }}" class="btn btn-sm btn-warning me-2"> 
                                                ✏️ Ubah
                                            </a>
                                            <form action="{{ route('admin.kategori.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
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
                                    <td colspan="3" class="text-center text-muted">Data kategori tidak tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
