<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    protected $fillable = [
        'meja_id',
        'menu_id',
        'qty',
        'jenis_pesanan',
        'nama_pelanggan',
        'nomor_wa',
        'tanggal_pesanan',
        'waktu_pesanan',
    ];

    public function meja()
    {
        return $this->belongsTo(Meja::class, 'meja_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cart) {
            $cart->setJenisPesanan();

            $menu = Menu::find($cart->menu_id);
            if (!$menu) {
                throw new Exception('Menu tidak ditemukan');
            }
            if ($menu->stok < $cart->qty) {
                throw new Exception('Stok tidak mencukupi');
            }
        });

        static::updating(function ($cart) {
            $menu = Menu::find($cart->menu_id);
            if (!$menu) {
                throw new Exception('Menu tidak ditemukan');
            }
            if ($menu->stok < $cart->qty) {
                throw new Exception('Stok tidak mencukupi');
            }
        });
    }

    public function setJenisPesanan()
    {
        if (!$this->jenis_pesanan) {
            $this->jenis_pesanan = $this->meja_id ? 'dine-in' : 'takeaway';
        }
    }

    public function setMejaIdAttribute($value)
    {
        $this->attributes['meja_id'] = $value;
        $this->attributes['jenis_pesanan'] = $value ? 'dine-in' : 'takeaway';
    }

public function setNomorWaAttribute($value)
{
    $jenis = $this->attributes['jenis_pesanan'] ?? null;
    $mejaId = $this->attributes['meja_id'] ?? null;

    // Kalau memang kamu butuh validasi, pakai cara yang aman
    if ($jenis === 'dine-in' && !$mejaId) {
        throw new \Exception('Meja ID harus diisi untuk pesanan dine-in');
    }

    $this->attributes['nomor_wa'] = $value;
}


    public static function kosongkanCart($identifier)
    {
        self::where(function ($query) use ($identifier) {
            $query->where('meja_id', $identifier)
                  ->orWhere('nomor_wa', $identifier);
        })->delete();
    }

    public function isTakeaway()
    {
        return $this->meja_id === null;
    }
}
