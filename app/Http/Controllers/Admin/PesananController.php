<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Kategori;
use App\Models\Meja;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with(['detailPesanan.menu', 'meja'])->orderBy('tanggal_pesanan', 'desc');

        if ($request->has('jenis') && in_array($request->jenis, ['dinein', 'takeaway'])) {
            $query->where('jenis_pesanan', $request->jenis);
        }

        $pesanans = $query->get();

        return view('admin.pages.pesanan.index', compact('pesanans'));
    }

    public function create()
    {
        $menus = Menu::all();
        $kategoris = Kategori::all();
        $mejas = Meja::all();

        return view('admin.pages.pesanan.create', compact('menus', 'kategoris', 'mejas'));
    }

    public function edit($id)
    {
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])->findOrFail($id);
        $menus = Menu::all();
        $mejas = Meja::all();
        $kategoris = Kategori::all();

        return view('admin.pages.pesanan.edit', compact('pesanan', 'menus', 'mejas', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);

        $request->validate([
            'status_pesanan' => 'required|in:pending,dibayar,selesai,dibatalkan',
        ]);

        $pesanan->status_pesanan = $request->status_pesanan;
        $pesanan->save();

        return redirect()->route('admin.pesanan.index')->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->delete();

        return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    public function show($id)
    {
        $pesanan = Pesanan::with('menu')->findOrFail($id);
        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_pesanan' => 'required|in:dinein,takeaway',
            'menu_id' => 'required|array',
            'menu_id.*' => 'exists:menu,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
            'tanggal_pesanan' => 'required|date',
            'waktu_pesanan' => 'required',
        ]);

        $jenisPesanan = $request->jenis_pesanan;
        $data = [
            'tanggal_pesanan' => $request->tanggal_pesanan,
            'waktu_pesanan' => $request->waktu_pesanan,
            'total_harga' => 0,
            'status_pesanan' => 'pending',
            'jenis_pesanan' => $jenisPesanan,
        ];

        if ($jenisPesanan === 'dinein') {
            $request->validate([
                'meja_id' => 'required|exists:meja,id',
            ]);
            $data['meja_id'] = $request->meja_id;
        } elseif ($jenisPesanan === 'takeaway') {
            $request->validate([
                'nama_pelanggan' => 'required|string|max:255',
                'nomor_wa' => 'required|string|max:20',
            ]);
            $data['nama_pelanggan'] = $request->nama_pelanggan;
            $data['nomor_wa'] = $request->nomor_wa;
            $data['meja_id'] = null;
        }

        // Simpan pesanan utama
        $pesanan = Pesanan::create($data);

        // Simpan detail pesanan
        $totalHarga = 0;
        foreach ($request->menu_id as $index => $menuId) {
            $menu = Menu::findOrFail($menuId);
            $jumlah = (int) $request->jumlah[$index];

            // Opsional: validasi jumlah tidak melebihi stok
            if ($jumlah > $menu->stok) {
                return redirect()->back()->withErrors([
                    'jumlah' => 'Jumlah untuk menu ' . $menu->nama_menu . ' melebihi stok yang tersedia.'
                ]);
            }

            $subtotal = $menu->harga * $jumlah;

            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'menu_id' => $menuId,
                'jumlah' => $jumlah,
                'subtotal' => $subtotal,
            ]);

            $totalHarga += $subtotal;
        }

        // Update total harga pesanan
        $pesanan->update([
            'total_harga' => $totalHarga,
        ]);

        return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dibuat.');
    }

    public function showBookingPage($jenis)
    {
        $kategoris = Kategori::with('menus')->get();

        if ($kategoris->isEmpty()) {
            return "Tidak ada kategori dengan menu.";
        }

        session(['jenis_pesanan' => $jenis]);

        return view("cart.$jenis.booking", compact('kategoris'));
    }

    public function processQR(Request $request)
    {
        $mejaId = $request->input('meja_id');

        if (!$mejaId) {
            return redirect()->route('home')->with('error', 'QR Code tidak valid!');
        }

        return redirect()->route('dinein.booking', ['meja' => $mejaId]);
    }

    public function showCustomerForm()
    {
        return view('cart.takeaway.customer-form');
    }

    public function saveCustomerData(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:20',
            'tanggal_pesanan' => 'required|date',
            'waktu_pesanan' => 'required',
        ]);

        session([
            'jenis_pesanan' => 'takeaway',
            'takeaway' => [
                'nama_pelanggan' => $request->nama_pelanggan,
                'nomor_wa' => $request->nomor_wa,
                'tanggal_pesanan' => $request->tanggal_pesanan,
                'waktu_pesanan' => $request->waktu_pesanan,
            ]
        ]);

        return redirect()->route('takeaway.booking')
            ->with('success', 'Data pelanggan berhasil disimpan. Silakan pilih menu.');
    }
}
