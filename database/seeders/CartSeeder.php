<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use Carbon\Carbon;

class CartSeeder extends Seeder
{
    public function run()
    {
        // Seed untuk pemesanan Dine-In
        Cart::create([
            'menu_id' => 1,                  // Sesuaikan dengan menu_id yang ada di tabel menu
            'meja_id' => 1,                  // Sesuaikan dengan meja yang tersedia (pastikan ID 1 ada)
            'qty' => 2,
            'jenis_pesanan' => 'dinein',
            'nama_pelanggan' => null,
            'nomor_wa' => null,
            'tanggal_pesanan' => null,
            'waktu_pesanan' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Seed untuk pemesanan Takeaway
        Cart::create([
            'menu_id' => 2,                  // Sesuaikan dengan menu_id yang ada
            'meja_id' => null,
            'qty' => 1,
            'jenis_pesanan' => 'takeaway',
            'nama_pelanggan' => 'Andi',
            'nomor_wa' => '081234567890',
            'tanggal_pesanan' => Carbon::now()->format('Y-m-d'),
            'waktu_pesanan' => '15:30',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
