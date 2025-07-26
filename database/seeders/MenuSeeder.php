<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $menus = [
            ['nama_menu' => 'Nasi', 'harga' => 5000, 'stok' => 20, 'id_kategori' => 1],
            ['nama_menu' => 'Ayam Goreng', 'harga' => 30000, 'stok' => 20, 'id_kategori' => 1],
        ];

        Menu::insert($menus);
        $this->command->info('✅ Seeder Menu berhasil dijalankan!');
    }
}
