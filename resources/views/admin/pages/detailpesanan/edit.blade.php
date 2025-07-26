@extends('admin.main')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Detail Pesanan</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.detailpesanan.index') }}">Detail Pesanan</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <form action="{{ route('admin.detailpesanan.update', $detailPesanan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">

                        <div class="form-group">
                            <label for="pesanan_info">Pesanan ID</label>
                            <input type="text" id="pesanan_info" class="form-control" value="{{ $detailPesanan->pesanan->id ?? '-' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="jenis_pesanan">Jenis Pesanan</label>
                            <input type="text" id="jenis_pesanan" class="form-control" value="{{ ucfirst($detailPesanan->pesanan->jenis_pesanan ?? '-') }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="menu_id">Menu</label>
                            <select name="menu_id" id="menu_id" class="form-control" required>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->id }}" {{ $menu->id == $detailPesanan->menu_id ? 'selected' : '' }}>
                                        {{ $menu->nama_menu }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" value="{{ old('jumlah', $detailPesanan->jumlah) }}" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="subtotal">Subtotal</label>
                            <input type="number" name="subtotal" id="subtotal" class="form-control" value="{{ old('subtotal', $detailPesanan->subtotal) }}" min="0" required>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.detailpesanan.index') }}" class="btn btn-sm btn-outline-secondary mr-2">Batal</a>
                            <button type="submit" class="btn btn-sm btn-warning">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
