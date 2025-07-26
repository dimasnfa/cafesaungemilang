@extends('admin.main')

@section('content')
    <div class="container">
        <h2>Edit Meja</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.meja.update', $meja->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nomor Meja</label>
                <input type="number" name="nomor_meja" class="form-control" value="{{ $meja->nomor_meja }}" required>
            </div>

            <div class="mb-3">
                <label>Tipe Meja</label>
                <select name="tipe_meja" class="form-control" required>
                    <option value="lesehan" {{ $meja->tipe_meja === 'lesehan' ? 'selected' : '' }}>Lesehan</option>
                    <option value="meja cafe" {{ $meja->tipe_meja === 'meja cafe' ? 'selected' : '' }}>Meja Cafe</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Lantai</label>
                <select name="lantai" class="form-control" required>
                    <option value="1" {{ $meja->lantai == 1 ? 'selected' : '' }}>Lantai 1</option>
                    <option value="2" {{ $meja->lantai == 2 ? 'selected' : '' }}>Lantai 2</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="tersedia" {{ $meja->status == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="terisi" {{ $meja->status == 'terisi' ? 'selected' : '' }}>Terisi</option>
                    <option value="dibersihkan" {{ $meja->status == 'dibersihkan' ? 'selected' : '' }}>Dibersihkan</option>
                </select>
            </div>

            <div class="mb-3">
                <label>QR Code</label><br>
                @if($meja->qr_code && file_exists(public_path($meja->qr_code)))
                    <img src="{{ asset($meja->qr_code) }}" width="150">
                @else
                    <span class="text-danger">QR Tidak Tersedia</span>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
