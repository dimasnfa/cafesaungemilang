<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\DNS2D;
use App\Models\Meja;

class MejaSeeder extends Seeder
{
    public function run()
    {
        // Matikan foreign key checks untuk menghindari error saat truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('meja')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Data meja yang akan di-seed
        $mejaData = [
            ['nomor_meja' => 1, 'tipe_meja' => 'lesehan', 'lantai' => '1', 'status' => 'tersedia'],
            ['nomor_meja' => 2, 'tipe_meja' => 'meja cafe', 'lantai' => '1', 'status' => 'tersedia'],
            ['nomor_meja' => 3, 'tipe_meja' => 'meja cafe', 'lantai' => '2', 'status' => 'tersedia'],
        ];

        // Inisialisasi Barcode Generator
        $barcode = new DNS2D();

        foreach ($mejaData as $data) {
            try {
                // Buat objek Meja
                $meja = Meja::create($data);

                // Buat URL untuk QR Code
                $url = url("/scan-meja/{$meja->id}");

                // Generate QR Code dalam bentuk base64
                $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($barcode->getBarcodePNG($url, 'QRCODE', 5, 5));

                // Update QR Code langsung ke database
                $meja->qr_code = $qrCodeBase64;
                $meja->save();

                $this->command->info("✅ QR Code berhasil dibuat untuk Meja {$meja->nomor_meja}");
            } catch (\Exception $e) {
                $this->command->error("❌ Gagal membuat QR Code untuk Meja {$data['nomor_meja']}: " . $e->getMessage());
            }
        }

        $this->command->info('✅ Seeder Meja berhasil dijalankan!');
    }
}
