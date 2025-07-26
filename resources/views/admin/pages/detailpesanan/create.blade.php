@extends('admin.main')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Tambah Detail Pesanan</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.detailpesanan.index') }}">Detail Pesanan</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Form Tambah Detail Pesanan</h5>
                </div>
                <form action="{{ route('admin.detailpesanan.store') }}" method="POST">
                    @csrf
                    <div class="card-body">

                        <div class="form-group">
                            <label for="pesanan_id">Pesanan</label>
                            <select name="pesanan_id" id="pesanan_id" class="form-control" required>
                                <option value="" disabled selected>Pilih Pesanan</option>
                                @foreach ($pesanans as $pesanan)
                                    <option value="{{ $pesanan->id }}" data-jenis="{{ $pesanan->jenis_pesanan }}">
                                        ID: {{ $pesanan->id }} -
                                        @if ($pesanan->jenis_pesanan === 'dinein')
                                            Meja {{ $pesanan->meja->nomor_meja ?? '-' }}
                                        @elseif ($pesanan->jenis_pesanan === 'takeaway')
                                            {{ $pesanan->nama_pelanggan ?? '-' }}
                                        @else
                                            -
                                        @endif
                                        ({{ ucfirst($pesanan->jenis_pesanan) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jenis_pesanan">Jenis Pesanan</label>
                            <input type="text" id="jenis_pesanan" class="form-control" readonly placeholder="Pilih pesanan terlebih dahulu">
                        </div>

                        <div class="form-group">
                            <label for="menu_id">Menu</label>
                            <select name="menu_id" id="menu_id" class="form-control" required>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->nama_menu }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="subtotal">Subtotal</label>
                            <input type="number" name="subtotal" id="subtotal" class="form-control" min="0" required>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.detailpesanan.index') }}" class="btn btn-sm btn-outline-secondary mr-2">Batal</a>
                            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const pesananSelect = document.getElementById('pesanan_id');
        const jenisPesananInput = document.getElementById('jenis_pesanan');

        pesananSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const jenis = selectedOption.getAttribute('data-jenis') || '';
            jenisPesananInput.value = jenis ? jenis.charAt(0).toUpperCase() + jenis.slice(1) : '';
        });
    </script>
    @endpush
@endsection
