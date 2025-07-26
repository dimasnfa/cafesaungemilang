@extends('admin.main')

@section('content')
    <div class="container">
        <h2>Tambah Meja</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.meja.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Nomor Meja</label>
                <input type="number" name="nomor_meja" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Tipe Meja</label>
                <select name="tipe_meja" class="form-control" required>
                    <option value="lesehan">Lesehan</option>
                    <option value="meja cafe">Meja Cafe</option> <!-- Sesuai validasi -->
                </select>
            </div>

            <div class="mb-3">
                <label>Lantai</label>
                <select name="lantai" class="form-control" required>
                    <option value="1">Lantai 1</option>
                    <option value="2">Lantai 2</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="tersedia">Tersedia</option>
                    <option value="terisi">Terisi</option>
                    <option value="dibersihkan">Dibersihkan</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
@endsection
