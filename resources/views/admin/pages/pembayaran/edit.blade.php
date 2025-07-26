@extends('admin.main')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Pembayaran</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.pembayaran.index') }}">Pembayaran</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <form action="{{ route('admin.pembayaran.update', $pembayaran->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="order_id">Order ID</label>
                            <input type="text" name="order_id" id="order_id" value="{{ $pembayaran->order_id }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="pesanan_id">Pesanan</label>
                            <select name="pesanan_id" id="pesanan_id" class="form-control" required>
                                @foreach($pesanans as $pesanan)
                                    <option value="{{ $pesanan->id }}"
                                        {{ $pembayaran->pesanan_id == $pesanan->id ? 'selected' : '' }}>
                                        PESANAN-{{ $pesanan->id }} - {{ $pesanan->jenis_pesanan }} - {{ $pesanan->meja?->nomor_meja ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="total_bayar">Total Bayar</label>
                            <input type="number" name="total_bayar" id="total_bayar" class="form-control" min="0"
                                   value="{{ $pembayaran->total_bayar }}" required>
                        </div>
                        <div class="form-group">
                            <label for="metode_pembayaran">Metode Pembayaran</label>
                            <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                                <option value="qris" {{ $pembayaran->metode_pembayaran == 'qris' ? 'selected' : '' }}>QRIS</option>
                                <option value="cash" {{ $pembayaran->metode_pembayaran == 'cash' ? 'selected' : '' }}>Cash</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status_pembayaran">Status Pembayaran</label>
                            <select name="status_pembayaran" id="status_pembayaran" class="form-control" required>
                                <option value="pending" {{ $pembayaran->status_pembayaran == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="dibayar" {{ $pembayaran->status_pembayaran == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                                <option value="gagal" {{ $pembayaran->status_pembayaran == 'gagal' ? 'selected' : '' }}>Gagal</option>
                                <option value="expired" {{ $pembayaran->status_pembayaran == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jenis_pesanan">Jenis Pesanan</label>
                            <select name="jenis_pesanan" id="jenis_pesanan" class="form-control" required>
                                <option value="dinein" {{ $pembayaran->jenis_pesanan == 'dinein' ? 'selected' : '' }}>Dine-In</option>
                                <option value="takeaway" {{ $pembayaran->jenis_pesanan == 'takeaway' ? 'selected' : '' }}>Takeaway</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nama_pelanggan">Nama Pelanggan</label>
                            <input type="text" name="nama_pelanggan" id="nama_pelanggan" value="{{ $pembayaran->nama_pelanggan }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="nomor_wa">Nomor WA</label>
                            <input type="text" name="nomor_wa" id="nomor_wa" value="{{ $pembayaran->nomor_wa }}" class="form-control">
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-secondary mr-2">Kembali</a>
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
