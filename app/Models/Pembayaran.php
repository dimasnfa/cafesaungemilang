<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'pesanan_id',
        'order_id',
        'total_bayar',
        'metode_pembayaran',
        'status_pembayaran',
        'jenis_pesanan',
        'nama_pelanggan',
        'nomor_wa',
        'nomor_meja',
        'tanggal_pesanan',
        'waktu_pesanan',
    ];

    protected $casts = [
        'total_bayar' => 'decimal:2',
        'status_pembayaran' => 'string',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pembayaran) {
            $pembayaran->tanggal_pesanan = now()->toDateString();
            $pembayaran->waktu_pesanan = now()->toTimeString();
        });
    }

    /**
     * Perbarui status pembayaran berdasarkan respons dari Midtrans
     */
    public function updateFromMidtrans($status)
    {
        if ($this->status_pembayaran === 'dibayar') {
            return;
        }

        $this->status_pembayaran = $this->mapMidtransStatus($status);
        $this->save();

        if ($this->status_pembayaran === 'dibayar') {
            $this->handleSuccessfulPayment();
        } elseif (in_array($this->status_pembayaran, ['gagal', 'expired'])) {
            $this->handleFailedPayment();
        }
    }

    /**
     * Mapping status Midtrans ke sistem pembayaran kita
     */
    protected function mapMidtransStatus($midtransStatus)
    {
        return match ($midtransStatus) {
            'settlement', 'capture' => 'dibayar',
            'pending' => 'pending',
            'cancel', 'expire', 'deny', 'failure' => 'gagal',
            default => $this->status_pembayaran,
        };
    }

    /**
     * Proses setelah pembayaran berhasil
     */
    protected function handleSuccessfulPayment()
    {
        if ($this->pesanan) {
            $this->pesanan->update(['status_pesanan' => 'dibayar']);
        }
    }

    /**
     * Proses setelah pembayaran gagal / expired
     */
    protected function handleFailedPayment()
    {
        if ($this->pesanan) {
            $this->pesanan->update(['status_pesanan' => 'dibatalkan']);
        }
    }
}
