<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\Pesanan;

class PembayaranSeeder extends Seeder
{
    public function run()
    {
        $pesananList = Pesanan::where('status_pesanan', 'pending')->get();

        if ($pesananList->isEmpty()) {
            $this->command->warn("⚠ Tidak ada pesanan pending! Pastikan ada pesanan sebelum menjalankan seeder.");
            return;
        }

        $metodePembayaran = ['QRIS', 'GoPay', 'Transfer Bank', 'Cash'];

        foreach ($pesananList as $pesanan) {
            Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'total_bayar' => $pesanan->total_harga,
                'metode_pembayaran' => $metodePembayaran[array_rand($metodePembayaran)],
                'status_pembayaran' => 'pending',
                'order_id' => Pembayaran::max('order_id') + 1,
                'jenis_pesanan' => 'dine-in',
                'nama_pelanggan' => $pesanan->nama_pelanggan,
                'nomor_wa' => $pesanan->nomor_wa,
            ]);
        }

        $this->command->info("✅ Seeder Pembayaran berhasil dijalankan!");
    }
}
