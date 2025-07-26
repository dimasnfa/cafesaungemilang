<?php

namespace Database\Seeders;

use App\Models\Kategori; // Sesuai dengan model yang kita buat sebelumnya
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        Kategori::create([
            'nama_kategori' => 'Makanan', // Menggunakan kolom 'nama_kategori' sesuai tabel
        ]);

        Kategori::create([
            'nama_kategori' => 'Minuman', // Contoh tambahan kategori lain
        ]);

        Kategori::create([
            'nama_kategori' => 'Nasi dan Mie', // Contoh tambahan kategori lain
        ]);
        Kategori::create([
            'nama_kategori' => 'Aneka Snack', // Contoh tambahan kategori lain
        ]);
        Kategori::create([
            'nama_kategori' => 'Paket Gemilang', // Contoh tambahan kategori lain
        ]);
    }
}
