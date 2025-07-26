<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pesanan;
use App\Models\Laporan;

class LaporanSeeder extends Seeder
{
    public function run()
    {
        $pesananHarian = Pesanan::where('status_pesanan', 'selesai')
            ->selectRaw('tanggal_pesanan, COUNT(*) as total_pesanan, SUM(total_harga) as total_pendapatan')
            ->groupBy('tanggal_pesanan')
            ->get();

        foreach ($pesananHarian as $data) {
            Laporan::updateOrCreate(
                ['tanggal_laporan' => $data->tanggal_pesanan],
                [
                    'total_pesanan' => $data->total_pesanan,
                    'total_pendapatan' => $data->total_pendapatan,
                ]
            );
        }
    }
}
