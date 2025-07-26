@extends('admin.main')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>➕ Tambah Menu</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.menu.index') }}">Menu</a></li>
                <li class="breadcrumb-item active">Tambah Menu</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Form Tambah Menu</h5>
                </div>
                <form action="{{ route('admin.menu.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <!-- Nama Menu -->
                        <div class="form-group">
                            <label for="nama_menu">Nama Menu</label>
                            <input type="text" name="nama_menu" id="nama_menu"
                                class="form-control @error('nama_menu') is-invalid @enderror"
                                value="{{ old('nama_menu') }}" placeholder="Masukkan nama menu (huruf & angka)">
                            @error('nama_menu')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Harga -->
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <input type="number" name="harga" id="harga"
                                class="form-control @error('harga') is-invalid @enderror"
                                value="{{ old('harga') }}" min="1" placeholder="Masukkan harga menu">
                            @error('harga')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Stok -->
                        <div class="form-group">
                            <label for="stok">Jumlah Stok</label>
                            <input type="number" name="stok" id="stok"
                                class="form-control @error('stok') is-invalid @enderror"
                                value="{{ old('stok') }}" min="1" placeholder="Masukkan jumlah stok menu">
                            @error('stok')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div class="form-group">
                            <label for="id_kategori">Kategori</label>
                            <select name="id_kategori" id="id_kategori"
                                class="form-control @error('id_kategori') is-invalid @enderror">
                                <option value="" disabled selected>Pilih Kategori</option>
                                @if(isset($kategori) && $kategori->count() > 0)
                                    @foreach ($kategori as $item)
                                        <option value="{{ $item->id }}" {{ old('id_kategori') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama_kategori }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada kategori tersedia</option>
                                @endif
                            </select>
                            @error('id_kategori')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary ml-2">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
