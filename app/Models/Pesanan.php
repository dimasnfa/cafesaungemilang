<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        'jenis_pesanan',
        'metode_pembayaran',
        'midtrans_order_id' // Tracking QRIS order ID
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'tanggal_pesanan' => 'date',
        'waktu_pesanan' => 'datetime:H:i:s'
    ];

    // Relasi ke Meja (nullable untuk takeaway)
    public function meja()
    {
        return $this->belongsTo(Meja::class, 'meja_id');
    }
    
    public function menu()
    {
        return $this->belongsToMany(Menu::class, 'detailpesanan', 'pesanan_id', 'menu_id')
                    ->withPivot('jumlah')
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

    // ✅ Method helper untuk display jenis pesanan
    public function getJenisPesananDisplayAttribute()
    {
        $jenis = strtolower($this->attributes['jenis_pesanan'] ?? '');
        return match($jenis) {
            'dinein' => 'Dine-In',
            'takeaway' => 'Takeaway',
            default => ucfirst($jenis)
        };
    }

    // ✅ Method untuk cek apakah bisa dihapus
    public function canBeDeleted()
    {
        // Tidak bisa hapus jika sudah dibayar atau selesai
        if (in_array($this->status_pesanan, ['dibayar', 'selesai'])) {
            return false;
        }

        // Bisa dihapus jika masih pending atau dibatalkan
        return in_array($this->status_pesanan, ['pending', 'dibatalkan']);
    }

    // ✅ Method untuk cek apakah pesanan dari QRIS
    public function isFromQris()
    {
        return $this->metode_pembayaran === 'qris' && 
               !empty($this->midtrans_order_id);
    }

    // ✅ Method untuk cek apakah takeaway
    public function isTakeaway()
    {
        return $this->jenis_pesanan === 'takeaway';
    }

    // ✅ Method untuk cek apakah dine-in
    public function isDineIn()
    {
        return $this->jenis_pesanan === 'dinein';
    }

    // ✅ PERBAIKAN UTAMA: Method untuk membuat pesanan dari QRIS settlement (Dine-in & Takeaway)
    public static function createFromQrisSettlement($orderData)
    {
        DB::beginTransaction();
        
        try {
            Log::info('🏗️ Creating pesanan from QRIS settlement', [
                'order_id' => $orderData['order_id'],
                'total_harga' => $orderData['total_harga'],
                'jenis_pesanan' => $orderData['jenis_pesanan']
            ]);

            // ✅ Validasi data berdasarkan jenis pesanan
            if ($orderData['jenis_pesanan'] === 'takeaway') {
                if (empty($orderData['nama_pelanggan']) || empty($orderData['nomor_wa'])) {
                    throw new \Exception('Data pelanggan takeaway tidak lengkap');
                }
            }

            if ($orderData['jenis_pesanan'] === 'dinein') {
                if (empty($orderData['meja_id'])) {
                    throw new \Exception('Data meja dine-in tidak lengkap');
                }
            }

            // ✅ Create pesanan dengan status pending (kasir yang akan konfirmasi)
            $pesananData = [
                'total_harga' => $orderData['total_harga'],
                'status_pesanan' => 'pending', // ✅ KUNCI: Status tetap pending sampai kasir konfirmasi
                'jenis_pesanan' => $orderData['jenis_pesanan'],
                'metode_pembayaran' => 'qris',
                'midtrans_order_id' => $orderData['order_id'],
                'tanggal_pesanan' => now()->toDateString(),
                'waktu_pesanan' => now()->toTimeString()
            ];

            // ✅ Data spesifik berdasarkan jenis pesanan
            if ($orderData['jenis_pesanan'] === 'dinein') {
                $pesananData['meja_id'] = $orderData['meja_id'];
                $pesananData['nama_pelanggan'] = null; // Dine-in tidak perlu nama
                $pesananData['nomor_wa'] = null; // Dine-in tidak perlu nomor WA
            } elseif ($orderData['jenis_pesanan'] === 'takeaway') {
                $pesananData['meja_id'] = null; // Takeaway tidak ada meja
                $pesananData['nama_pelanggan'] = $orderData['nama_pelanggan'];
                $pesananData['nomor_wa'] = $orderData['nomor_wa'];
            }

            $pesanan = self::create($pesananData);

            Log::info('✅ Pesanan created successfully', [
                'pesanan_id' => $pesanan->id,
                'order_id' => $orderData['order_id'],
                'jenis_pesanan' => $pesanan->jenis_pesanan
            ]);

            // ✅ Create detail pesanan
            if (isset($orderData['items']) && is_array($orderData['items'])) {
                foreach ($orderData['items'] as $item) {
                    if (isset($item['menu_id'], $item['quantity'], $item['price'])) {
                        $detail = DetailPesanan::create([
                            'pesanan_id' => $pesanan->id,
                            'menu_id' => $item['menu_id'],
                            'jumlah' => $item['quantity'],
                            'harga_satuan' => $item['price'],
                            'subtotal' => $item['price'] * $item['quantity']
                        ]);

                        Log::info('✅ Detail pesanan created', [
                            'detail_id' => $detail->id,
                            'menu_id' => $item['menu_id'],
                            'quantity' => $item['quantity']
                        ]);
                    }
                }
            }

            // ✅ KUNCI: Create pembayaran dengan status "dibayar" karena sudah settlement
            $pembayaranData = [
                'pesanan_id' => $pesanan->id,
                'order_id' => $orderData['order_id'],
                'total_bayar' => $orderData['total_harga'],
                'metode_pembayaran' => 'qris',
                'status_pembayaran' => 'dibayar', // ✅ Langsung dibayar karena settlement
                'jenis_pesanan' => $orderData['jenis_pesanan'],
                'settlement_time' => now(),
                'tanggal_pesanan' => now()->toDateString(),
                'waktu_pesanan' => now()->toTimeString()
            ];

            // ✅ Data pembayaran spesifik berdasarkan jenis pesanan
            if ($orderData['jenis_pesanan'] === 'dinein') {
                $pembayaranData['nama_pelanggan'] = null;
                $pembayaranData['nomor_wa'] = null;
                $pembayaranData['nomor_meja'] = $orderData['nomor_meja'] ?? null;
            } elseif ($orderData['jenis_pesanan'] === 'takeaway') {
                $pembayaranData['nama_pelanggan'] = $orderData['nama_pelanggan'];
                $pembayaranData['nomor_wa'] = $orderData['nomor_wa'];
                $pembayaranData['nomor_meja'] = null;
            }

            $pembayaran = Pembayaran::create($pembayaranData);

            Log::info('✅ Pembayaran created successfully', [
                'pembayaran_id' => $pembayaran->id,
                'status_pembayaran' => 'dibayar',
                'jenis_pesanan' => $orderData['jenis_pesanan']
            ]);

            DB::commit();

            Log::info('✅ Successfully created pesanan from QRIS settlement', [
                'pesanan_id' => $pesanan->id,
                'pembayaran_id' => $pembayaran->id,
                'order_id' => $orderData['order_id'],
                'jenis_pesanan' => $pesanan->jenis_pesanan,
                'status_pesanan' => $pesanan->status_pesanan,
                'status_pembayaran' => $pembayaran->status_pembayaran
            ]);

            return $pesanan;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('❌ Failed to create pesanan from QRIS settlement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $orderData
            ]);
            
            throw $e;
        }
    }

    // ✅ Method untuk get status badge class
    public function getStatusBadgeClass()
    {
        return match ($this->status_pesanan) {
            'pending' => 'warning',
            'dibayar' => 'info',
            'selesai' => 'success',
            'dibatalkan' => 'danger',
            default => 'secondary'
        };
    }

    // ✅ Method untuk get status text
    public function getStatusText()
    {
        return match ($this->status_pesanan) {
            'pending' => 'Menunggu Konfirmasi',
            'dibayar' => 'Sedang Diproses',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status_pesanan)
        };
    }

    // ✅ Method untuk get info lengkap pesanan
    public function getInfoLengkap()
    {
        $info = [
            'id' => $this->id,
            'jenis_pesanan' => $this->getJenisPesananDisplayAttribute(),
            'status_pesanan' => $this->getStatusText(),
            'total_harga' => number_format($this->total_harga, 0, ',', '.'),
            'metode_pembayaran' => strtoupper($this->metode_pembayaran),
            'tanggal_pesanan' => $this->tanggal_pesanan,
            'waktu_pesanan' => $this->waktu_pesanan,
        ];

        // Info spesifik berdasarkan jenis pesanan
        if ($this->isDineIn()) {
            $info['meja'] = $this->meja ? $this->meja->nomor_meja : '-';
            $info['pelanggan'] = 'Dine-In (Meja ' . ($this->meja ? $this->meja->nomor_meja : '-') . ')';
        } elseif ($this->isTakeaway()) {
            $info['pelanggan'] = $this->nama_pelanggan ?? '-';
            $info['kontak'] = $this->nomor_wa ?? '-';
        }

        return $info;
    }

    // Event handler
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pesanan) {
            // Set tanggal dan waktu otomatis jika belum diset
            if (!$pesanan->tanggal_pesanan) {
                $pesanan->tanggal_pesanan = now()->toDateString();
            }
            if (!$pesanan->waktu_pesanan) {
                $pesanan->waktu_pesanan = now()->toTimeString();
            }

            Log::info('Creating pesanan', [
                'jenis_pesanan' => $pesanan->jenis_pesanan,
                'metode_pembayaran' => $pesanan->metode_pembayaran,
                'total_harga' => $pesanan->total_harga
            ]);
        });

        // ✅ Event deleting untuk handle foreign key
        static::deleting(function ($pesanan) {
            Log::info('Attempting to delete pesanan', [
                'pesanan_id' => $pesanan->id,
                'jenis_pesanan' => $pesanan->jenis_pesanan,
                'status' => $pesanan->status_pesanan
            ]);

            // Hapus detail pesanan terlebih dahulu
            $pesanan->detailPesanan()->delete();
            
            // Hapus pembayaran jika ada
            if ($pesanan->pembayaran) {
                $pesanan->pembayaran->delete();
            }
        });

        static::updating(function ($pesanan) {
            if ($pesanan->isDirty('status_pesanan')) {
                $originalStatus = $pesanan->getOriginal('status_pesanan');

                Log::info('Status pesanan berubah', [
                    'pesanan_id' => $pesanan->id,
                    'jenis_pesanan' => $pesanan->jenis_pesanan,
                    'from' => $originalStatus,
                    'to' => $pesanan->status_pesanan,
                    'metode_pembayaran' => $pesanan->metode_pembayaran
                ]);

                // ✅ Handle status change untuk semua metode pembayaran dan jenis pesanan
                if ($pesanan->status_pesanan === 'dibayar' && $originalStatus === 'pending') {
                    // Kurangi stok menu
                    foreach ($pesanan->detailPesanan as $detail) {
                        if ($detail->menu) {
                            $detail->menu->kurangiStok($detail->jumlah);
                            Log::info('Stock reduced for menu', [
                                'menu_id' => $detail->menu->id,
                                'quantity' => $detail->jumlah,
                                'jenis_pesanan' => $pesanan->jenis_pesanan
                            ]);
                        }
                    }

                    // ✅ UNTUK CASH (hanya dine-in): Update status pembayaran otomatis
                    if ($pesanan->metode_pembayaran === 'cash' && 
                        $pesanan->isDineIn() &&
                        $pesanan->pembayaran && 
                        $pesanan->pembayaran->status_pembayaran === 'pending') {
                        
                        $pesanan->pembayaran->update(['status_pembayaran' => 'dibayar']);
                        Log::info('Status pembayaran cash auto-updated to dibayar for dine-in');
                    }
                    
                    // ✅ UNTUK QRIS (dine-in & takeaway): Status pembayaran sudah dibayar dari settlement
                    if ($pesanan->metode_pembayaran === 'qris') {
                        Log::info('QRIS pesanan status updated to dibayar by kasir', [
                            'jenis_pesanan' => $pesanan->jenis_pesanan,
                            'order_id' => $pesanan->midtrans_order_id
                        ]);
                    }
                }

                // ✅ Kembalikan stok jika pesanan dibatalkan
                if ($pesanan->status_pesanan === 'dibatalkan' && $originalStatus !== 'dibatalkan') {
                    foreach ($pesanan->detailPesanan as $detail) {
                        if ($detail->menu) {
                            $detail->menu->kembalikanStok($detail->jumlah);
                            Log::info('Stock restored for menu', [
                                'menu_id' => $detail->menu->id,
                                'quantity' => $detail->jumlah,
                                'jenis_pesanan' => $pesanan->jenis_pesanan
                            ]);
                        }
                    }

                    // Update status pembayaran jika dibatalkan
                    if ($pesanan->pembayaran && $pesanan->pembayaran->status_pembayaran !== 'dibatalkan') {
                        $pesanan->pembayaran->update(['status_pembayaran' => 'dibatalkan']);
                        Log::info('Status pembayaran updated to dibatalkan', [
                            'jenis_pesanan' => $pesanan->jenis_pesanan
                        ]);
                    }
                }
            }
        });
    }
}