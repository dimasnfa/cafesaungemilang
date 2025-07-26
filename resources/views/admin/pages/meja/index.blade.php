@extends('admin.main')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Daftar Meja</h2>
            <a href="{{ route('admin.meja.create') }}" class="btn btn-sm btn-success">➕ Tambah Meja</a>
        </div>

        @php
            $ngrokUrl = config('app.webhook_url');
        @endphp

        <table class="table table-bordered">
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
                    <td>{{ $meja->id }}</td>
                    <td>{{ $meja->nomor_meja }}</td>
                    <td>{{ ucfirst($meja->tipe_meja) }}</td>
                    <td>{{ $meja->lantai }}</td>
                    <td>
                        @if($meja->status === 'tersedia')
                            <span class="badge bg-success">Tersedia</span>
                        @elseif($meja->status === 'terisi')
                            <span class="badge bg-warning">Terisi</span>
                        @else
                            <span class="badge bg-danger">Dibersihkan</span>
                        @endif
                    </td>
                    <td>
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
                    <td>
                        <a href="{{ route('admin.meja.edit', $meja->id) }}" class="btn btn-warning btn-sm">Edit</a>
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
@endsection
