<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use App\Models\Menu;
use Illuminate\Http\Request;

class DetailPesananController extends Controller
{
    public function index()
    {
        // Menggunakan eager loading agar relasi pesanan dan menu bisa diakses di view
        $detailPesanans = DetailPesanan::with(['pesanan', 'menu'])->get();

        return view('admin.pages.detailpesanan.index', compact('detailPesanans'));
    }

    public function create()
    {
        // Ambil data pesanan dan menu
        $pesanans = Pesanan::all();
        $menus = Menu::all();

        return view('admin.pages.detailpesanan.create', compact('pesanans', 'menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pesanan_id' => 'required|exists:pesanan,id', // Ubah pesanans ke pesanan
            'menu_id' => 'required|exists:menu,id', // Pastikan tabel 'menu' benar
            'jumlah' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
        ]);
        

        DetailPesanan::create($request->all());

        return redirect()->route('admin.detailpesanan.index')->with('success', 'Detail pesanan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $detailPesanan = DetailPesanan::findOrFail($id);
        $pesanans = Pesanan::all();
        $menus = Menu::all();

        return view('admin.pages.detailpesanan.edit', compact('detailPesanan', 'pesanans', 'menus'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $detailPesanan = DetailPesanan::findOrFail($id);
        $detailPesanan->update($request->all());

        return redirect()->route('admin.detailpesanan.index')->with('success', 'Detail pesanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $detailPesanan = DetailPesanan::findOrFail($id);
        $detailPesanan->delete();

        return redirect()->route('admin.detailpesanan.index')->with('success', 'Detail pesanan berhasil dihapus.');
    }
}
