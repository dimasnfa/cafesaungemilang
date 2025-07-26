<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    use HasFactory;

    // Tabel yang digunakan
    protected $table = 'meja';

    // Kolom yang boleh diisi
    protected $fillable = [
        'nomor_meja', 
        'tipe_meja',
        'lantai', 
        'status', 
        'qr_code'
    ];

    // Tidak perlu validasi di model karena sudah ditangani di controller
}
