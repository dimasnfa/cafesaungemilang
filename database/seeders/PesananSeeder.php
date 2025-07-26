<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pesanan;
use Carbon\Carbon;

class PesananSeeder extends Seeder
{
    public function run()
    {
        $statusList = ['pending', 'dibayar', 'selesai', 'dibatalkan'];

        $pesananData = [
            [
                'nama_pelanggan'   => 'Pelanggan 1',
                'jenis_pesanan'    => 'dinein', // diperbaiki dari 'dine-in'
                'tanggal_pesanan'  => Carbon::now()->subDays(1)->toDateString(),
                'waktu_pesanan'    => '12:00:00',
                'total_harga'      => 85000,
                'status_pesanan'   => $statusList[array_rand($statusList)],
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'nama_pelanggan'   => 'Pelanggan 2',
                'jenis_pesanan'    => 'dinein',
                'tanggal_pesanan'  => Carbon::now()->subDays(2)->toDateString(),
                'waktu_pesanan'    => '14:30:00',
                'total_harga'      => 92000,
                'status_pesanan'   => $statusList[array_rand($statusList)],
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'nama_pelanggan'   => 'Pelanggan 3',
                'jenis_pesanan'    => 'takeaway',
                'tanggal_pesanan'  => Carbon::now()->subDays(3)->toDateString(),
                'waktu_pesanan'    => '17:00:00',
                'total_harga'      => 105000,
                'status_pesanan'   => $statusList[array_rand($statusList)],
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ];

        foreach ($pesananData as $pesanan) {
            Pesanan::create($pesanan);
        }

        $this->command->info("✅ 3 Pesanan berhasil dibuat: 2 dinein, 1 takeaway");
    }
}
