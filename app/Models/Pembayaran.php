<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        'settlement_time'
    ];

    protected $casts = [
        'total_bayar' => 'decimal:2',
        'settlement_time' => 'datetime'
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pembayaran) {
            // Set tanggal dan waktu otomatis jika belum diset
            if (!$pembayaran->tanggal_pesanan) {
                $pembayaran->tanggal_pesanan = now()->toDateString();
            }
            if (!$pembayaran->waktu_pesanan) {
                $pembayaran->waktu_pesanan = now()->toTimeString();
            }
            
            // ✅ PERBAIKAN: Set status pembayaran berdasarkan metode dan konteks
            if (empty($pembayaran->status_pembayaran)) {
                if ($pembayaran->metode_pembayaran === 'cash') {
                    $pembayaran->status_pembayaran = 'pending';
                } elseif ($pembayaran->metode_pembayaran === 'qris') {
                    // ✅ KUNCI: Untuk QRIS dari settlement, langsung dibayar
                    // Untuk QRIS dari form biasa, masih pending
                    $pembayaran->status_pembayaran = 'pending'; // Default pending
                }
            }
        });
    }

    // ✅ PERBAIKAN: Method khusus untuk create pembayaran QRIS settlement
    public static function createFromQrisSettlement($orderData)
    {
        return self::create([
            'order_id' => $orderData['order_id'],
            'total_bayar' => $orderData['total_harga'],
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'dibayar', // ✅ Langsung dibayar karena settlement
            'jenis_pesanan' => $orderData['jenis_pesanan'],
            'nama_pelanggan' => $orderData['nama_pelanggan'] ?? null,
            'nomor_wa' => $orderData['nomor_wa'] ?? null,
            'nomor_meja' => $orderData['nomor_meja'] ?? null,
            'settlement_time' => now()
        ]);
    }

    // ✅ Method untuk cek apakah QRIS sudah settled
    public function isQrisSettled()
    {
        return $this->metode_pembayaran === 'qris' && 
               $this->status_pembayaran === 'dibayar' &&
               !is_null($this->settlement_time);
    }

    // ✅ Method untuk cek apakah pembayaran ready untuk konfirmasi kasir
    public function isReadyForCashierConfirmation()
    {
        return $this->status_pembayaran === 'dibayar' && 
               $this->pesanan && 
               $this->pesanan->status_pesanan === 'pending';
    }

    // ✅ Method untuk mendapatkan badge class berdasarkan status
    public function getStatusBadgeClass()
    {
        return match ($this->status_pembayaran) {
            'dibayar' => 'success',
            'pending' => 'warning', 
            'gagal' => 'danger',
            default => 'secondary'
        };
    }

    // ✅ Method untuk mendapatkan text status yang user-friendly
    public function getStatusText()
    {
        return match ($this->status_pembayaran) {
            'dibayar' => 'Dibayar',
            'pending' => 'Pending',
            'gagal' => 'Gagal',
            default => ucfirst($this->status_pembayaran)
        };
    }

    // ✅ Method untuk mendapatkan informasi lengkap pembayaran
    public function getPaymentSummary()
    {
        return [
            'order_id' => $this->order_id,
            'total_bayar' => $this->total_bayar,
            'metode_pembayaran' => $this->metode_pembayaran,
            'status_pembayaran' => $this->status_pembayaran,
            'status_text' => $this->getStatusText(),
            'badge_class' => $this->getStatusBadgeClass(),
            'jenis_pesanan' => $this->jenis_pesanan,
            'nama_pelanggan' => $this->nama_pelanggan,
            'nomor_wa' => $this->nomor_wa,
            'nomor_meja' => $this->nomor_meja,
            'settlement_time' => $this->settlement_time,
            'is_qris_settled' => $this->isQrisSettled(),
            'ready_for_confirmation' => $this->isReadyForCashierConfirmation()
        ];
    }
}