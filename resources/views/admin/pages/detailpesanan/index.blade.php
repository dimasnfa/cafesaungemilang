@extends('admin.main') 

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Detail Pesanan</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Detail Pesanan</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">

                <div class="card-body">
                    {{-- Tambahkan table-responsive agar bisa di-slide horizontal --}}
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Pesanan ID</th>
                                    <th>Jenis Pesanan</th> {{-- Kolom baru --}}
                                    <th>Menu</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($detailPesanans as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ optional($detail->pesanan)->id ?? 'Tidak Diketahui' }}</td>
                                        <td>
                                            {{ optional($detail->pesanan)->jenis_pesanan ?? '-' }}
                                        </td>
                                        <td>{{ optional($detail->menu)->nama_menu ?? 'Tidak Ada Menu' }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('admin.detailpesanan.edit', $detail->id) }}" class="btn btn-sm btn-warning">
                                                    ✏️ Ubah
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Data detail pesanan tidak tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Penutup table-responsive --}}
                </div>

            </div>
        </div>
    </div>
@endsection
