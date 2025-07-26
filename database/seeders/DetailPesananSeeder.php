<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use App\Models\Menu;

class DetailPesananSeeder extends Seeder
{
    public function run()
    {
        $pesananList = Pesanan::all();
        $menuList = Menu::all();

        if ($pesananList->isEmpty() || $menuList->isEmpty()) {
            $this->command->warn("⚠ Tidak ada pesanan atau menu ditemukan. Pastikan sudah menjalankan seeder sebelumnya!");
            return;
        }

        foreach ($pesananList as $pesanan) {
            // Hitung jumlah menu yang tersedia
            $maxMenuCount = $menuList->count();

            // Pilih jumlah menu acak (maksimal jumlah menu yang tersedia)
            $pickCount = rand(1, min(3, $maxMenuCount));
            $menus = $menuList->random($pickCount);

            foreach ($menus as $menu) {
                $jumlah = rand(1, 3);
                $subtotal = $menu->harga * $jumlah;

                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id'    => $menu->id,
                    'jumlah'     => $jumlah,
                    'subtotal'   => $subtotal,
                ]);
            }
        }

        $this->command->info('✅ Seeder DetailPesanan berhasil dijalankan!');
    }
}
