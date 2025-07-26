<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin|kasir'); // Akses hanya untuk admin dan kasir
    }

    /**
     * Tampilkan semua data menu
     */
    public function index()
    {
        $menus = Menu::with('kategori')->get(); // Ambil menu beserta kategori
        return view('admin.pages.menu.index', compact('menus'));
    }

    /**
     * Tampilkan form tambah menu
     */
    public function create()
    {
        $kategori = Kategori::all(); // Ambil semua kategori
        return view('admin.pages.menu.create', compact('kategori'));
    }

    /**
     * Simpan data menu baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'id_kategori' => 'required|exists:kategori,id',
            'nama_menu'   => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s]+$/',
            ],
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
        ], [
            'nama_menu.regex' => 'Nama menu hanya boleh mengandung huruf, angka, dan spasi.',
        ]);

        Menu::create($validated);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit menu
     */
    public function edit($id)
    {
        $menu = Menu::findOrFail($id); // Cari menu berdasarkan ID
        $kategori = Kategori::all(); // Ambil semua kategori
        return view('admin.pages.menu.edit', compact('menu', 'kategori'));
    }

    /**
     * Perbarui data menu
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'id_kategori' => 'required|exists:kategori,id',
            'nama_menu'   => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s]+$/',
            ],
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
        ], [
            'nama_menu.regex' => 'Nama menu hanya boleh mengandung huruf, angka, dan spasi.',
        ]);

        $menu = Menu::findOrFail($id);
        $menu->update($validated);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diupdate!');
    }

    /**
     * Hapus data menu
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dihapus!');
    }

    /**
     * Tampilkan halaman rekomendasi menu berdasarkan tipe
     */
    public function rekomendasi($tipe)
    {
        return view('menu.rekomendasi', ['tipe' => $tipe]);
    }
}
