<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Pembayaran;
use App\Models\Pesanan;

class MidtransPollingController extends Controller
{
    /**
     * ✅ PERBAIKAN UTAMA: CEK STATUS - Sesuai konsep baru untuk Dine-in & Takeaway
     * - Untuk QRIS: Langsung assume settlement tanpa cek API Midtrans
     * - Untuk Cash: Langsung redirect ke success (hanya dine-in)
     * - Trigger dari button "Check Status" Snap JS Midtrans
     */
    public function cekStatus($order_id)
    {
        Log::info('🔍 Mulai cek status pembayaran - MidtransPooling', [
            'order_id' => $order_id,
            'timestamp' => now()
        ]);

        // ✅ LOGIC CASH: Langsung redirect tanpa cek API (hanya untuk dine-in)
        if (strpos($order_id, 'CASH-') === 0) {
            return $this->handleCashPayment($order_id);
        }

        // ✅ LOGIC QRIS: Langsung assume settlement tanpa cek API Midtrans
        // Karena di sandbox QRIS pasti settlement
        // Berlaku untuk QRIS-DINEIN dan QRIS-TAKEAWAY
        return $this->handleQrisAssumedSettlement($order_id);
    }

    /**
     * ✅ HANDLE CASH PAYMENT - Tidak berubah (hanya dine-in)
     */
    private function handleCashPayment($order_id)
    {
        $pembayaran = Pembayaran::where('order_id', $order_id)->with('pesanan')->first();
        
        if (!$pembayaran) {
            Log::error('❌ Data pembayaran cash tidak ditemukan', ['order_id' => $order_id]);
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        Log::info('✅ Pembayaran cash - redirect ke sukses', [
            'order_id' => $order_id,
            'jenis_pesanan' => $pembayaran->jenis_pesanan,
        ]);

        return $this->redirectToSuccess($pembayaran->jenis_pesanan, 'cash');
    }

    /**
     * ✅ PERBAIKAN: HANDLE QRIS ASSUMED SETTLEMENT untuk Dine-in & Takeaway
     * Core function yang menggantikan cek API Midtrans
     * Langsung assume QRIS sudah settlement dan buat pesanan
     */
    private function handleQrisAssumedSettlement($order_id)
    {
        try {
            Log::info('💰 QRIS Assumed Settlement - Processing', ['order_id' => $order_id]);

            // ✅ Cek apakah pesanan sudah ada di database
            $existingPesanan = Pesanan::where('midtrans_order_id', $order_id)
                                     ->with('pembayaran')
                                     ->first();

            if ($existingPesanan) {
                // Pesanan sudah ada, langsung redirect ke success
                Log::info('✅ Pesanan sudah ada, redirect ke success', [
                    'order_id' => $order_id,
                    'pesanan_id' => $existingPesanan->id,
                    'jenis_pesanan' => $existingPesanan->jenis_pesanan
                ]);

                return $this->redirectToSuccess($existingPesanan->jenis_pesanan, 'qris');
            }

            // ✅ Pesanan belum ada, create dari cache data
            $qrisData = Cache::get("qris_data_{$order_id}");
            
            if (!$qrisData) {
                Log::error('❌ QRIS data cache not found', ['order_id' => $order_id]);
                return back()->with('error', 'Data pesanan QRIS tidak ditemukan. Silakan pesan ulang.');
            }

            // ✅ PERBAIKAN: Deteksi jenis pesanan dari order_id atau cache
            $jenisPesanan = $this->detectJenisPesananFromOrderId($order_id, $qrisData);

            Log::info('🔍 Detected jenis pesanan', [
                'order_id' => $order_id,
                'jenis_pesanan' => $jenisPesanan,
                'source' => 'order_id_pattern_and_cache'
            ]);

            // ✅ Create pesanan dari assumed settlement
            $pesanan = $this->createPesananFromAssumedSettlement($order_id, $qrisData);
            
            if (!$pesanan) {
                return back()->with('error', 'Gagal membuat pesanan dari QRIS settlement.');
            }

            Log::info('✅ Pesanan berhasil dibuat dari QRIS assumed settlement', [
                'order_id' => $order_id,
                'pesanan_id' => $pesanan->id,
                'jenis_pesanan' => $pesanan->jenis_pesanan
            ]);

            // ✅ Hapus cache setelah berhasil
            Cache::forget("qris_data_{$order_id}");
            
            return $this->redirectToSuccess($pesanan->jenis_pesanan, 'qris');

        } catch (\Exception $e) {
            Log::error('❌ Error handling QRIS assumed settlement', [
                'order_id' => $order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memproses QRIS settlement.');
        }
    }

    /**
     * ✅ BARU: DETECT JENIS PESANAN dari Order ID pattern
     */
    private function detectJenisPesananFromOrderId($order_id, $qrisData)
    {
        // ✅ Deteksi dari pattern order_id
        if (strpos($order_id, 'QRIS-DINEIN-') === 0) {
            return 'dinein';
        }
        
        if (strpos($order_id, 'QRIS-TAKEAWAY-') === 0) {
            return 'takeaway';
        }

        // ✅ Fallback: ambil dari cache data
        return $qrisData['jenis_pesanan'] ?? 'dinein';
    }

    /**
     * ✅ PERBAIKAN: CREATE PESANAN FROM ASSUMED SETTLEMENT untuk Dine-in & Takeaway
     */
    private function createPesananFromAssumedSettlement($order_id, $qrisData)
    {
        try {
            Log::info('🏗️ Creating pesanan from assumed settlement', [
                'order_id' => $order_id,
                'jenis_pesanan' => $qrisData['jenis_pesanan'],
                'qris_data_keys' => array_keys($qrisData)
            ]);

            // ✅ Prepare data untuk create pesanan (berbeda untuk dine-in & takeaway)
            $orderData = [
                'order_id' => $order_id,
                'total_harga' => $qrisData['total_harga'],
                'jenis_pesanan' => $qrisData['jenis_pesanan'],
                'items' => $qrisData['items'] ?? []
            ];

            // ✅ Data khusus untuk dine-in
            if ($qrisData['jenis_pesanan'] === 'dinein') {
                $orderData['meja_id'] = $qrisData['meja_id'] ?? null;
                $orderData['nomor_meja'] = $qrisData['nomor_meja'] ?? null;
                $orderData['nama_pelanggan'] = null; // Dine-in tidak ada nama pelanggan
                $orderData['nomor_wa'] = null; // Dine-in tidak ada nomor WA
            }

            // ✅ Data khusus untuk takeaway
            if ($qrisData['jenis_pesanan'] === 'takeaway') {
                $orderData['meja_id'] = null; // Takeaway tidak ada meja
                $orderData['nomor_meja'] = null;
                $orderData['nama_pelanggan'] = $qrisData['nama_pelanggan'] ?? null;
                $orderData['nomor_wa'] = $qrisData['nomor_wa'] ?? null;
            }

            // ✅ Create pesanan menggunakan model method
            $pesanan = Pesanan::createFromQrisSettlement($orderData);

            Log::info('✅ Successfully created pesanan from assumed settlement', [
                'pesanan_id' => $pesanan->id,
                'order_id' => $order_id,
                'jenis_pesanan' => $pesanan->jenis_pesanan,
                'status_pesanan' => $pesanan->status_pesanan,
                'status_pembayaran' => $pesanan->pembayaran->status_pembayaran
            ]);

            return $pesanan;

        } catch (\Exception $e) {
            Log::error('❌ Failed to create pesanan from assumed settlement', [
                'order_id' => $order_id,
                'error' => $e->getMessage(),
                'qris_data' => $qrisData
            ]);
            throw $e;
        }
    }

    /**
     * ✅ PERBAIKAN: REDIRECT TO SUCCESS - Support dine-in & takeaway
     */
    private function redirectToSuccess($jenisPesanan, $metodePembayaran = 'qris')
    {
        $pesan = $metodePembayaran === 'cash' 
                ? 'Pembayaran cash berhasil dicatat. Silakan bayar ke kasir.'
                : 'Pembayaran QRIS berhasil! Kasir akan memproses pesanan Anda.';

        Log::info('✅ Redirect ke halaman sukses', [
            'jenis_pesanan' => $jenisPesanan,
            'metode_pembayaran' => $metodePembayaran,
        ]);

        $route = match ($jenisPesanan) {
            'dinein' => 'cart.dinein.checkout.success',
            'takeaway' => 'cart.takeaway.checkout.success', // ✅ PERBAIKAN: Tambah route takeaway
            default => 'home'
        };

        return redirect()->route($route)->with('success', $pesan);
    }

    /**
     * ✅ SAVE QRIS DATA TO CACHE - Tidak berubah
     */
    public function saveQrisDataToCache($order_id, $qrisData)
    {
        try {
            // Save data ke cache selama 30 menit
            $cacheDuration = now()->addMinutes(30);
            
            Cache::put("qris_data_{$order_id}", $qrisData, $cacheDuration);
            
            Log::info('✅ QRIS data saved to cache', [
                'order_id' => $order_id,
                'jenis_pesanan' => $qrisData['jenis_pesanan'] ?? 'unknown',
                'expires_at' => $cacheDuration,
                'data_keys' => array_keys($qrisData)
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('❌ Failed to save QRIS data to cache', [
                'order_id' => $order_id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * ✅ API ENDPOINT - Check pesanan baru untuk notifikasi kasir
     */
    public function checkNewPesanan()
    {
        try {
            // Cek pesanan yang baru masuk dan belum dinotifikasi (dine-in & takeaway)
            $newOrders = Pesanan::whereIn('status_pesanan', ['pending'])
                               ->where('created_at', '>=', now()->subMinutes(10))
                               ->with(['pembayaran', 'meja'])
                               ->orderBy('created_at', 'desc')
                               ->get();

            Log::info('🔔 Checking for new orders (dine-in & takeaway)', [
                'found_orders' => $newOrders->count(),
                'timestamp' => now()
            ]);

            if ($newOrders->count() > 0) {
                // ✅ Group by jenis pesanan untuk info yang lebih detail
                $ordersByType = $newOrders->groupBy('jenis_pesanan');
                $dineinCount = $ordersByType->get('dinein', collect())->count();
                $takeawayCount = $ordersByType->get('takeaway', collect())->count();

                return response()->json([
                    'new_pesanan' => true,
                    'count' => $newOrders->count(),
                    'dinein_count' => $dineinCount,
                    'takeaway_count' => $takeawayCount,
                    'message' => 'Ada ' . $newOrders->count() . ' pesanan baru (Dine-in: ' . $dineinCount . ', Takeaway: ' . $takeawayCount . ')',
                    'pooling_status' => 'new_orders_found',
                    'timestamp' => now()->timestamp
                ]);
            }

            return response()->json([
                'new_pesanan' => false,
                'count' => 0,
                'dinein_count' => 0,
                'takeaway_count' => 0,
                'message' => 'Tidak ada pesanan baru',
                'pooling_status' => 'no_new_orders',
                'timestamp' => now()->timestamp
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error checking new pesanan', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'new_pesanan' => false,
                'message' => 'Error checking new orders',
                'pooling_status' => 'error',
                'timestamp' => now()->timestamp
            ], 500);
        }
    }

    /**
     * ✅ PERBAIKAN: API ENDPOINT - Simplified untuk assumed settlement (dine-in & takeaway)
     */
    public function cekStatusApi($order_id)
    {
        Log::info('🔍 API cek status dimulai', ['order_id' => $order_id]);

        try {
            // ✅ Untuk cash, return status dari database (hanya dine-in)
            if (strpos($order_id, 'CASH-') === 0) {
                $pembayaran = Pembayaran::where('order_id', $order_id)->with('pesanan')->first();
                
                if (!$pembayaran) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pembayaran tidak ditemukan'
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'status' => 'pending',
                    'payment_type' => 'cash',
                    'gross_amount' => $pembayaran->total_bayar,
                    'order_status' => $pembayaran->pesanan?->status_pesanan ?? 'pending',
                    'jenis_pesanan' => $pembayaran->jenis_pesanan ?? 'dinein',
                    'message' => 'Pembayaran cash menunggu konfirmasi kasir'
                ]);
            }

            // ✅ Untuk QRIS (dine-in & takeaway), langsung return settlement status
            $existingPesanan = Pesanan::where('midtrans_order_id', $order_id)->first();
            
            if ($existingPesanan) {
                // Pesanan sudah dibuat
                return response()->json([
                    'success' => true,
                    'status' => 'settlement',
                    'payment_type' => 'qris',
                    'gross_amount' => $existingPesanan->total_harga,
                    'order_status' => $existingPesanan->status_pesanan,
                    'jenis_pesanan' => $existingPesanan->jenis_pesanan,
                    'message' => 'QRIS settlement berhasil - ' . ucfirst($existingPesanan->jenis_pesanan)
                ]);
            } else {
                // Pesanan belum dibuat, tapi assume settlement
                $qrisData = Cache::get("qris_data_{$order_id}");
                $jenisPesanan = $this->detectJenisPesananFromOrderId($order_id, $qrisData ?? []);
                
                return response()->json([
                    'success' => true,
                    'status' => 'settlement',
                    'payment_type' => 'qris',
                    'gross_amount' => $qrisData['total_harga'] ?? 0,
                    'jenis_pesanan' => $jenisPesanan,
                    'message' => 'QRIS settlement ready to process - ' . ucfirst($jenisPesanan)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Exception API cek status', [
                'order_id' => $order_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}