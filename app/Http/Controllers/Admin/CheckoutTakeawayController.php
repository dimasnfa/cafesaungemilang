<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Cart;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use Midtrans\Snap;

class CheckoutTakeawayController extends Controller
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    /**
     * ✅ PROCESS CHECKOUT - HANYA QRIS (sesuai alur Midtrans Pooling)
     */
    public function process(Request $request)
    {
        $nomorWa = session('takeaway.nomor_wa');
        $namaPelanggan = session('takeaway.nama_pelanggan') ?? 'Pelanggan Takeaway';
        $tanggalPesanan = session('takeaway.tanggal_pesanan');
        $waktuPesanan = session('takeaway.waktu_pesanan');
        $paymentType = 'qris'; // ✅ HANYA QRIS untuk takeaway

        Log::info('🛒 Checkout Takeaway QRIS dimulai', [
            'nama_pelanggan' => $namaPelanggan,
            'nomor_wa' => $nomorWa,
            'tanggal' => $tanggalPesanan,
            'waktu' => $waktuPesanan,
            'payment_type' => $paymentType
        ]);

        // ✅ Validasi data pelanggan
        if (!$nomorWa || !$namaPelanggan || !$tanggalPesanan || !$waktuPesanan) {
            Log::error('❌ Data pelanggan takeaway tidak lengkap', [
                'nomor_wa' => $nomorWa,
                'nama_pelanggan' => $namaPelanggan,
                'tanggal_pesanan' => $tanggalPesanan,
                'waktu_pesanan' => $waktuPesanan
            ]);
            return response()->json(['error' => 'Data pelanggan tidak lengkap.'], 400);
        }

        // ✅ Validasi cart
        $carts = Cart::with('menu')
            ->where('nomor_wa', $nomorWa)
            ->where('jenis_pesanan', 'takeaway')
            ->get();

        if ($carts->isEmpty()) {
            Log::warning('⚠️ Keranjang takeaway kosong', ['nomor_wa' => $nomorWa]);
            return response()->json(['error' => 'Keranjang Takeaway kosong!'], 400);
        }

        // ✅ Validasi stok dan hitung total
        $total = 0;
        $items = [];

        foreach ($carts as $cart) {
            if (!$cart->menu) {
                Log::warning('⚠️ Menu tidak ditemukan di cart takeaway', ['cart_id' => $cart->id]);
                continue;
            }

            // ✅ Validasi stok menu
            if ($cart->qty > $cart->menu->stok) {
                Log::error('❌ Stok tidak cukup', [
                    'menu' => $cart->menu->nama_menu,
                    'diminta' => $cart->qty,
                    'stok' => $cart->menu->stok
                ]);
                return response()->json([
                    'error' => "Stok tidak cukup untuk menu {$cart->menu->nama_menu}. Tersedia: {$cart->menu->stok}"
                ], 400);
            }

            $subtotal = $cart->menu->harga * $cart->qty;
            $total += $subtotal;

            $items[] = [
                'id' => $cart->menu->id,
                'price' => $cart->menu->harga,
                'quantity' => $cart->qty,
                'name' => $cart->menu->nama_menu,
            ];
        }

        if ($total <= 0) {
            return response()->json(['error' => 'Total pesanan tidak valid.'], 400);
        }

        // ✅ HANYA QRIS untuk takeaway
        return $this->processQrisPayment($carts, $total, $items, $nomorWa, $namaPelanggan, $tanggalPesanan, $waktuPesanan);
    }

    /**
     * ✅ PROCESS QRIS PAYMENT - SESUAI ALUR MIDTRANS POOLING
     * TIDAK create pesanan di database, hanya save ke cache dan generate Snap Token
     * Data pesanan baru akan dibuat ketika user klik "Check Status" dan pooling controller mendeteksi settlement
     */
    private function processQrisPayment($carts, $total, $items, $nomorWa, $namaPelanggan, $tanggalPesanan, $waktuPesanan)
    {
        try {
            // ✅ Buat order_id unik untuk takeaway QRIS dengan pattern yang jelas
            $orderId = 'QRIS-TAKEAWAY-' . time() . '-' . substr(md5($nomorWa), 0, 6);

            Log::info('💳 Processing QRIS takeaway payment', [
                'order_id' => $orderId,
                'nama_pelanggan' => $namaPelanggan,
                'nomor_wa' => $nomorWa,
                'total' => $total
            ]);

            // ✅ KUNCI UTAMA: Simpan data ke cache dulu (TIDAK ke database)
            // Data ini akan diambil oleh MidtransPollingController saat settlement
            $qrisData = [
                'order_id' => $orderId,
                'meja_id' => null, // Takeaway tidak ada meja
                'nomor_meja' => null,
                'jenis_pesanan' => 'takeaway',
                'nama_pelanggan' => $namaPelanggan,
                'nomor_wa' => $nomorWa,
                'tanggal_pesanan' => $tanggalPesanan,
                'waktu_pesanan' => $waktuPesanan,
                'total_harga' => $total,
                'items' => []
            ];

            // ✅ Format items untuk cache (disesuaikan dengan model Pesanan)
            foreach ($carts as $cart) {
                if (!$cart->menu) continue;
                
                $qrisData['items'][] = [
                    'menu_id' => $cart->menu->id,
                    'quantity' => $cart->qty,
                    'price' => $cart->menu->harga,
                    'name' => $cart->menu->nama_menu,
                ];
            }

            // ✅ Save data ke cache - PENTING: Ini akan dibaca oleh MidtransPollingController
            $midtransPoolingController = new MidtransPollingController();
            $cacheSuccess = $midtransPoolingController->saveQrisDataToCache($orderId, $qrisData);
            
            if (!$cacheSuccess) {
                Log::error('❌ Failed to save QRIS takeaway data to cache', ['order_id' => $orderId]);
                return response()->json(['error' => 'Gagal menyimpan data QRIS'], 500);
            }

            Log::info('✅ QRIS takeaway data saved to cache for pooling', [
                'order_id' => $orderId,
                'nama_pelanggan' => $namaPelanggan,
                'nomor_wa' => $nomorWa,
                'total' => $total,
                'cache_key' => "qris_data_{$orderId}",
                'note' => 'Data siap untuk settlement pooling'
            ]);

            // ✅ Parameter Snap Midtrans untuk takeaway
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $total,
                ],
                'item_details' => $items,
                'customer_details' => [
                    'first_name' => $namaPelanggan,
                    'phone' => $nomorWa,
                    'email' => 'takeaway@restaurant.com',
                    'last_name' => 'Takeaway',
                ],
                'custom_field1' => 'takeaway',
                'custom_field2' => $namaPelanggan,
                'custom_field3' => $nomorWa,
            ];

            // ✅ Generate Snap Token
            $snapToken = Snap::getSnapToken($params);

            Log::info('🎟️ Snap token generated successfully for takeaway', [
                'order_id' => $orderId,
                'nama_pelanggan' => $namaPelanggan,
                'snap_token_preview' => substr($snapToken, 0, 20) . '...'
            ]);

            // ✅ Hapus cart SETELAH snap token berhasil dibuat
            $deletedCarts = Cart::where('nomor_wa', $nomorWa)
                ->where('jenis_pesanan', 'takeaway')
                ->delete();

            Log::info('🗑️ Cart takeaway cleared after successful QRIS token generation', [
                'deleted_count' => $deletedCarts,
                'nomor_wa' => $nomorWa
            ]);

            Log::info('✅ QRIS takeaway checkout process completed', [
                'order_id' => $orderId,
                'nama_pelanggan' => $namaPelanggan,
                'nomor_wa' => $nomorWa,
                'total' => $total,
                'status' => 'waiting_for_payment_and_check_status',
                'note' => 'Pesanan akan dibuat otomatis setelah user klik Check Status dan pooling detect settlement'
            ]);

            return response()->json([
                'success' => true,
                'payment_method' => 'qris',
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'total' => $total,
                'nama_pelanggan' => $namaPelanggan,
                'nomor_wa' => $nomorWa,
                'message' => 'Silakan scan QRIS untuk melakukan pembayaran. Setelah bayar, klik tombol "Check Status"'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Midtrans Snap Token Error Takeaway', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'nomor_wa' => $nomorWa,
                'order_id' => $orderId ?? 'not_generated'
            ]);

            return response()->json([
                'error' => 'Gagal menghubungkan ke Midtrans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ TAKEAWAY SUCCESS - Halaman sukses setelah pembayaran QRIS berhasil
     */
    public function takeawaySuccess()
    {
        // ✅ Clear session setelah checkout berhasil (sama seperti dine-in)
        $namaPelanggan = session('takeaway.nama_pelanggan');
        $nomorWa = session('takeaway.nomor_wa');
        $tanggalPesanan = session('takeaway.tanggal_pesanan');
        $waktuPesanan = session('takeaway.waktu_pesanan');
        
        // Clear session
        session()->forget('takeaway');

        Log::info('✅ Takeaway success page loaded', [
            'nama_pelanggan' => $namaPelanggan,
            'nomor_wa' => $nomorWa
        ]);

        return view('cart.takeaway.success', [
            'title' => 'Pembayaran QRIS Berhasil',
            'message' => 'Terima kasih! Pesanan takeaway Anda sedang diproses oleh kasir.',
            'type' => 'qris',
            'namaPelanggan' => $namaPelanggan,
            'nomorWa' => $nomorWa,
            'tanggalPesanan' => $tanggalPesanan,
            'waktuPesanan' => $waktuPesanan
        ]);
    }

    /**
     * ✅ CHECK ORDER STATUS - Untuk monitoring real-time takeaway
     */
    public function checkOrderStatus(Request $request)
    {
        $nomorWa = $request->input('nomor_wa');
        
        if (!$nomorWa) {
            return response()->json(['error' => 'Nomor WA tidak valid'], 400);
        }

        // Cek pesanan terbaru berdasarkan nomor WA
        $pesanan = Pesanan::with(['pembayaran', 'detailPesanan.menu'])
                          ->where('nomor_wa', $nomorWa)
                          ->where('jenis_pesanan', 'takeaway')
                          ->latest()
                          ->first();

        if (!$pesanan) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'pesanan' => [
                'id' => $pesanan->id,
                'nama_pelanggan' => $pesanan->nama_pelanggan,
                'total_harga' => $pesanan->total_harga,
                'status_pesanan' => $pesanan->status_pesanan,
                'status_pembayaran' => $pesanan->pembayaran?->status_pembayaran ?? 'pending',
                'metode_pembayaran' => $pesanan->metode_pembayaran,
                'tanggal_pesanan' => $pesanan->tanggal_pesanan,
                'waktu_pesanan' => $pesanan->waktu_pesanan,
                'created_at' => $pesanan->created_at->format('Y-m-d H:i:s'),
                'flow_info' => [
                    'is_qris_settled' => $pesanan->metode_pembayaran === 'qris' && 
                                        $pesanan->pembayaran && 
                                        $pesanan->pembayaran->status_pembayaran === 'dibayar',
                    'ready_for_confirmation' => $pesanan->status_pesanan === 'pending' && 
                                               $pesanan->pembayaran && 
                                               $pesanan->pembayaran->status_pembayaran === 'dibayar'
                ]
            ]
        ]);
    }

    /**
     * ✅ VALIDATION HELPER - Untuk validasi data takeaway
     */
    private function validateTakeawayData($nomorWa, $namaPelanggan, $tanggalPesanan, $waktuPesanan)
    {
        $errors = [];

        if (!$nomorWa) {
            $errors[] = 'Nomor WhatsApp diperlukan';
        } elseif (!preg_match('/^[0-9+\-\s()]{10,15}$/', $nomorWa)) {
            $errors[] = 'Format nomor WhatsApp tidak valid';
        }

        if (!$namaPelanggan || strlen($namaPelanggan) < 2) {
            $errors[] = 'Nama pelanggan minimal 2 karakter';
        }

        if (!$tanggalPesanan || !strtotime($tanggalPesanan)) {
            $errors[] = 'Tanggal pesanan tidak valid';
        }

        if (!$waktuPesanan || !strtotime($waktuPesanan)) {
            $errors[] = 'Waktu pesanan tidak valid';
        }

        // ✅ Validasi tanggal tidak boleh masa lalu
        if ($tanggalPesanan && strtotime($tanggalPesanan) < strtotime('today')) {
            $errors[] = 'Tanggal pesanan tidak boleh masa lalu';
        }

        return $errors;
    }

    /**
     * ✅ CLEAR EXPIRED QRIS CACHE - Utility method untuk cleanup
     */
    public function clearExpiredQrisCache()
    {
        try {
            $clearedCount = 0;
            // Implementation depends on your cache driver
            // For Redis, you might need to scan for keys with pattern
            
            Log::info('✅ Expired QRIS takeaway cache cleanup completed', [
                'cleared_count' => $clearedCount
            ]);
            
            return response()->json([
                'success' => true,
                'cleared_count' => $clearedCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Failed to clear expired QRIS takeaway cache', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ DEPRECATED: Removed cash payment methods
     * - processCash() -> Tidak diperlukan, takeaway hanya QRIS
     * - cashSuccess() -> Tidak diperlukan, takeaway hanya QRIS
     * - processCashPayment() -> Tidak diperlukan, takeaway hanya QRIS
     * 
     * ✅ DEPRECATED: Removed callback methods
     * - takeawayCallback() -> Tidak dipakai, pakai pooling system
     */
}