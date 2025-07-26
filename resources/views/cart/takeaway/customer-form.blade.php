@extends('layouts.formcustomer')

@section('content')
<form action="{{ route('takeaway.customer.save') }}" method="POST" class="customer-form">
    @csrf

    {{-- Tampilkan error jika ada --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-group">
        <label for="nama_pelanggan">👤 Nama Pelanggan</label>
        <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" placeholder="Masukkan nama Anda" required>
    </div>

    <div class="form-group">
        <label for="nomor_wa">📱 Nomor WhatsApp</label>
        <input type="text" id="nomor_wa" name="nomor_wa" value="{{ old('nomor_wa') }}" placeholder="08xxxxxxxxxx" required>
    </div>

    <div class="form-group">
        <label for="tanggal_pesanan">📅 Tanggal Pesanan</label>
        <input type="date" id="tanggal_pesanan" name="tanggal_pesanan" value="{{ old('tanggal_pesanan') }}" required>
    </div>

    <div class="form-group">
        <label for="waktu_pesanan">⏰ Waktu Ambil Pesanan</label>
        <input type="time" id="waktu_pesanan" name="waktu_pesanan" value="{{ old('waktu_pesanan') }}" required>
    </div>

    <div class="form-actions">
        <a href="{{ url('/') }}" class="btn-cancel">Batal</a>
        <button type="submit" class="btn-submit">Konfirmasi</button>
    </div>
</form>
@endsection
