@extends('admin.main')

@section('content')
<div class="container mt-4">
    <h2>Tambah Pesanan</h2>
    <form action="{{ route('admin.pesanan.store') }}" method="POST">
        @csrf
        
        <div id="menu-container">
            <label class="form-label">Menu & Jumlah</label>
            <div class="menu-row row mb-2">
                <div class="col-md-5">
                    <select name="menu_id[]" class="form-control menu-select" required>
                        <option value="">-- Pilih Menu --</option>
                        @foreach ($menus as $menu)
                            <option value="{{ $menu->id }}" 
                                    data-harga="{{ $menu->harga }}" 
                                    data-stok="{{ $menu->stok }}">
                                {{ $menu->nama_menu }} - Rp {{ number_format($menu->harga) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" placeholder="Jumlah" required>
                </div>
                <div class="col-md-4">
                    <div class="stok-info" style="display: none;">
                        <span class="badge status-stok"></span>
                        <small class="stok-tersedia"></small>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-sm btn-secondary mb-3" id="tambah-menu">+ Tambah Menu</button>

        {{-- Tanggal dan Waktu Pesanan --}}
        <div class="mb-3">
            <label for="tanggal_pesanan" class="form-label">Tanggal Pesanan</label>
            <input type="date" name="tanggal_pesanan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="waktu_pesanan" class="form-label">Waktu Pesanan</label>
            <input type="time" name="waktu_pesanan" class="form-control" required>
        </div>

        {{-- Jenis Pesanan --}}
        <div class="mb-3">
            <label for="jenis_pesanan" class="form-label">Jenis Pesanan</label>
            <select name="jenis_pesanan" class="form-control" required id="jenis_pesanan">
                <option value="">-- Pilih --</option>
                <option value="dinein">Dine-in</option>
                <option value="takeaway">Takeaway</option>
            </select>
        </div>

        {{-- Form Dine-in --}}
        <div id="form-dinein" style="display: none;">
            <div class="mb-3">
                <label for="meja_id" class="form-label">Nomor Meja</label>
                <select name="meja_id" class="form-control" id="mejaSelect">
                    @foreach ($mejas as $item)
                        <option value="{{ $item->id }}" data-tipe="{{ $item->tipe_meja }}">
                            {{ $item->nomor_meja }} - {{ $item->tipe_meja }} - Lantai {{ $item->lantai }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="tipe_meja_display" class="form-label">Tipe Meja</label>
                <input type="text" class="form-control" id="tipeMejaDisplay" readonly>
            </div>
        </div>

        {{-- Form Takeaway --}}
        <div id="form-takeaway" style="display: none;">
            <div class="mb-3">
                <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan" class="form-control">
            </div>
            <div class="mb-3">
                <label for="nomor_wa" class="form-label">Nomor WhatsApp</label>
                <input type="text" name="nomor_wa" class="form-control">
            </div>
        </div>

        {{-- Total --}}
        <div class="mt-3">
            <strong>Total: Rp <span id="total-harga">0</span></strong>
        </div>

        <input type="hidden" name="status_pesanan" value="pending">

        {{-- Submit --}}
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

{{-- Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const jenisPesananSelect = document.getElementById('jenis_pesanan');
        const formDinein = document.getElementById('form-dinein');
        const formTakeaway = document.getElementById('form-takeaway');
        const mejaSelect = document.getElementById('mejaSelect');
        const tipeMejaDisplay = document.getElementById('tipeMejaDisplay');
        const totalHargaSpan = document.getElementById('total-harga');

        function toggleForms() {
            const jenis = jenisPesananSelect.value;
            formDinein.style.display = jenis === 'dinein' ? 'block' : 'none';
            formTakeaway.style.display = jenis === 'takeaway' ? 'block' : 'none';
        }

        function updateTipeMeja() {
            const selected = mejaSelect.options[mejaSelect.selectedIndex];
            const tipe = selected.getAttribute('data-tipe');
            tipeMejaDisplay.value = tipe ?? '';
        }

        // Perhitungan dan stok
        function hitungTotal() {
            let total = 0;
            document.querySelectorAll('.menu-row').forEach(row => {
                const select = row.querySelector('.menu-select');
                const jumlahInput = row.querySelector('.jumlah-input');
                const stokInfo = row.querySelector('.stok-info');
                const statusStok = row.querySelector('.status-stok');
                const stokTersedia = row.querySelector('.stok-tersedia');

                const selected = select.options[select.selectedIndex];
                const harga = parseFloat(selected.getAttribute('data-harga') || 0);
                const stok = parseInt(selected.getAttribute('data-stok') || 0);
                const qty = parseInt(jumlahInput.value || 0);

                if (select.value) {
                    stokInfo.style.display = 'block';
                    if (stok > 0) {
                        statusStok.className = 'badge bg-success status-stok';
                        statusStok.textContent = 'Ready';
                    } else {
                        statusStok.className = 'badge bg-danger status-stok';
                        statusStok.textContent = 'Tidak Tersedia';
                    }
                    stokTersedia.textContent = ' Stok: ' + stok;
                } else {
                    stokInfo.style.display = 'none';
                }

                if (!isNaN(harga) && !isNaN(qty)) {
                    total += harga * qty;
                }
            });

            totalHargaSpan.textContent = total.toLocaleString('id-ID');
        }

        // Event listener
        jenisPesananSelect.addEventListener('change', toggleForms);
        mejaSelect.addEventListener('change', updateTipeMeja);
        toggleForms();
        updateTipeMeja();

        document.getElementById('menu-container').addEventListener('input', hitungTotal);
        document.getElementById('menu-container').addEventListener('change', hitungTotal);

        // Tambah baris menu baru
        document.getElementById('tambah-menu').addEventListener('click', () => {
            const container = document.getElementById('menu-container');
            const firstRow = container.querySelector('.menu-row');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelector('.menu-select').value = '';
            newRow.querySelector('.jumlah-input').value = '';
            const stokInfo = newRow.querySelector('.stok-info');
            stokInfo.style.display = 'none';
            stokInfo.querySelector('.status-stok').textContent = '';
            stokInfo.querySelector('.stok-tersedia').textContent = '';

            container.appendChild(newRow);
        });

        hitungTotal();
    });
</script>
@endsection
