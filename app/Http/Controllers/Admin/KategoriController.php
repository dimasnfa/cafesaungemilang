<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KategoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin|kasir'); // Admin dan Kasir bisa mengakses kategori
    }

    public function index()
    {
        $kategori = Kategori::all();
        return view('admin.pages.kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('admin.pages.kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => [
                'required',
                'unique:kategori,nama_kategori',
                'regex:/^[a-zA-Z0-9\s]+$/',
                'max:50'
            ]
        ], [
            'nama_kategori.regex' => 'Nama kategori hanya boleh mengandung huruf, angka, dan spasi.',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.pages.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => [
                'required',
                "unique:kategori,nama_kategori,{$kategori->id}",
                'regex:/^[a-zA-Z0-9\s]+$/',
                'max:50'
            ]
        ], [
            'nama_kategori.regex' => 'Nama kategori hanya boleh mengandung huruf, angka, dan spasi.',
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
