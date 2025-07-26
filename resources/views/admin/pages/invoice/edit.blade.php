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
                            <label>ID Pesanan</label>
                            <p class="form-control-static">PESANAN-{{ $pembayaran->pesanan_id }}</p>
                        </div>
                        <div class="form-group">
                            <label for="status_pembayaran">Status Pembayaran</label>
                            <select name="status_pembayaran" id="status_pembayaran" class="form-control">
                                <option value="pending" {{ $pembayaran->status_pembayaran == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="dibayar" {{ $pembayaran->status_pembayaran == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                                <option value="dibatalkan" {{ $pembayaran->status_pembayaran == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">Update</button>
                        <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
