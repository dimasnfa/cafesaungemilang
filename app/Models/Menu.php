<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = [
        'nama_menu',
        'harga',
        'stok', // Stok tersedia
        'id_kategori',
        'is_rekomendasi', // tambahkan ini
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id');
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'menu_id');
    }

    // Fungsi untuk mengurangi stok
    public function kurangiStok($jumlah)
    {
        if ($this->stok >= $jumlah) {
            $this->stok -= $jumlah;
            $this->save();
        }
    }

    // Fungsi untuk mengembalikan stok saat pesanan dibatalkan
    public function kembalikanStok($jumlah)
    {
        $this->stok += $jumlah;
        $this->save();
    }
}
