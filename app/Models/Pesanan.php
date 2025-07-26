<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'meja_id',
        'nama_pelanggan',
        'nomor_wa',
        'tanggal_pesanan',
        'waktu_pesanan',
        'total_harga',
        'status_pesanan',
        'jenis_pesanan'
    ];

    // HAPUS protected $casts['is_takeaway'] karena tidak ada lagi field tersebut

    // Relasi ke Meja
    public function meja()
    {
        return $this->belongsTo(Meja::class, 'meja_id');
    }
    
    public function menu()
{
    return $this->belongsToMany(Menu::class, 'detailpesanan', 'pesanan_id', 'menu_id')
                ->withPivot('jumlah') // atau 'qty', sesuaikan nama kolom
                ->withTimestamps();
}


    // Relasi ke DetailPesanan
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }

    // Relasi ke Pembayaran
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'pesanan_id');
    }



    // Accessor untuk menentukan jenis pesanan berdasarkan ada/tidaknya meja_id
    public function getJenisPesananAttribute()
    {
        return $this->meja_id ? 'Dine-In' : 'Takeaway';
    }

    // Event handler
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pesanan) {
            $pesanan->tanggal_pesanan = now()->toDateString();
            $pesanan->waktu_pesanan = now()->toTimeString();
        });

        static::updating(function ($pesanan) {
            if ($pesanan->isDirty('status_pesanan')) {
                $originalStatus = $pesanan->getOriginal('status_pesanan');

                // Kurangi stok jika status berubah menjadi "dibayar"
                if ($pesanan->status_pesanan === 'dibayar' && !in_array($originalStatus, ['dibayar', 'selesai'])) {
                    foreach ($pesanan->detailPesanan as $detail) {
                        if ($detail->menu) {
                            $detail->menu->kurangiStok($detail->jumlah);
                        }
                    }
                }

                // Kembalikan stok jika pesanan dibatalkan
                if ($pesanan->status_pesanan === 'dibatalkan' && $originalStatus !== 'dibatalkan') {
                    foreach ($pesanan->detailPesanan as $detail) {
                        if ($detail->menu) {
                            $detail->menu->kembalikanStok($detail->jumlah);
                        }
                    }
                }
            }
        });
    }
}
