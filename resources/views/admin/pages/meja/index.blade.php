@extends('admin.main')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Data Meja</h2>
        <a href="{{ route('admin.meja.create') }}" class="btn btn-sm btn-success">➕ Tambah Meja</a>
    </div>

    @php
        $ngrokUrl = config('app.webhook_url');
    @endphp

    <style>
        thead tr th {
            background-color: #007bff !important; /* Biru Bootstrap */
            color: white !important;
            text-align: center;
            vertical-align: middle;
        }
    </style>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nomor Meja</th>
                    <th>Tipe Meja</th>
                    <th>Lantai</th>
                    <th>Status</th>
                    <th>QR Code</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mejas as $meja)
                <tr>
                    <td class="text-center">{{ $meja->id }}</td>
                    <td class="text-center">{{ $meja->nomor_meja }}</td>
                    <td class="text-center">{{ ucfirst($meja->tipe_meja) }}</td>
                    <td class="text-center">{{ $meja->lantai }}</td>
                    <td class="text-center">
                        @if($meja->status === 'tersedia')
                            <span class="badge bg-success">Tersedia</span>
                        @elseif($meja->status === 'terisi')
                            <span class="badge bg-warning">Terisi</span>
                        @else
                            <span class="badge bg-danger">Dibersihkan</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($meja->qr_code)
                            <img src="{{ asset($meja->qr_code) }}" alt="QR Code" width="100" class="mb-1"><br>
                            <a href="{{ route('cart.dinein.booking.by.meja', $meja->id) }}" target="_blank" class="d-block mb-1">🔗 Booking Meja</a>
                            @if($ngrokUrl)
                                <a href="{{ rtrim($ngrokUrl, '/') . '/dinein/booking/' . $meja->id }}" target="_blank" class="d-block mb-1">🔗 Booking via Ngrok</a>
                            @endif
                            <a href="{{ asset($meja->qr_code) }}" download class="btn btn-sm btn-success mt-1">⬇️ Download QR</a>
                        @else
                            <span class="text-danger">QR Code Tidak Tersedia</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.meja.edit', $meja->id) }}" class="btn btn-warning btn-sm mb-1">Edit</a>
                        <form action="{{ route('admin.meja.destroy', $meja->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus QR meja ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
