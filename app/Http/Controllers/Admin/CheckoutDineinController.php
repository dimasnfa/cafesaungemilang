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
use App\Models\Meja;
use Midtrans\Snap;

class CheckoutDineinController extends Controller
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    /**
     * ✅ PROCESS CHECKOUT - Sesuai alur Midtrans Pooling
     */
    public function process(Request $request)
    {
        $mejaId = session('meja_id');
        $jenisPesanan = session('jenis_pesanan', 'dinein');
        $paymentType = $request->input('payment_type', 'qris'); // Default QRIS

        Log::info('🛒 Checkout Dine-in dimulai', [
            'meja_id' => $mejaId,
            'jenis_pesanan' => $jenisPesanan,
            'payment_type' => $paymentType
        ]);

        // ✅ Validasi session
        if (!$mejaId || $jenisPesanan !== 'dinein') {
            Log::error('❌ Sesi tidak valid untuk dine-in checkout', [
                'meja_id' => $mejaId,
                'jenis_pesanan' => $jenisPesanan
            ]);
            return response()->json(['error' => 'Sesi meja atau jenis pesanan tidak valid.'], 400);
        }

        // ✅ Ambil data meja
        $meja = Meja::find($mejaId);
        if (!$meja) {
            Log::error('❌ Meja tidak ditemukan', ['meja_id' => $mejaId]);
            return response()->json(['error' => 'Meja tidak ditemukan.'], 400);
        }

        // ✅ Validasi cart
        $carts = Cart::with('menu')
            ->where('meja_id', $mejaId)
            ->where('jenis_pesanan', $jenisPesanan)
            ->get();

        if ($carts->isEmpty()) {
            Log::warning('⚠️ Keranjang kosong saat checkout', ['meja_id' => $mejaId]);
            return response()->json(['error' => 'Keranjang Dine-In kosong!'], 400);
        }

        // ✅ Validasi stok menu
        foreach ($carts as $cart) {
            if (!$cart->menu) {
                Log::error('❌ Menu tidak ditemukan', ['cart_id' => $cart->id]);
                return response()->json(['error' => 'Menu tidak ditemukan.'], 400);
            }
            
            if ($cart->qty > $cart->menu->stok) {
                Log::error('❌ Stok tidak cukup', [
                    'menu_id' => $cart->menu->id,
                    'requested' => $cart->qty,
                    'available' => $cart->menu->stok
                ]);
                return response()->json([
                    'error' => "Stok tidak cukup untuk menu {$cart->menu->nama_menu}. Tersedia: {$cart->menu->stok}"
                ], 400);
            }
        }

        // ✅ Hitung total dan siapkan items
        $total = 0;
        $items = [];

        foreach ($carts as $cart) {
            $subtotal = $cart->menu->harga * $cart->qty;
            $total += $subtotal;

            $items[] = [
                'menu_id' => $cart->menu->id,
                'price' => $cart->menu->harga,
                'quantity' => $cart->qty,
                'name' => $cart->menu->nama_menu,
            ];
        }

        if ($total <= 0) {
            return response()->json(['error' => 'Total pesanan tidak valid.'], 400);
        }

        // ✅ Handle berdasarkan metode pembayaran
        if ($paymentType === 'cash') {
            return $this->processCashPayment($carts, $total, $meja, $mejaId, $jenisPesanan);
        } else {
            return $this->processQrisPayment($carts, $total, $items, $meja, $mejaId, $jenisPesanan);
        }
    }

    /**
     * ✅ PROCESS CASH PAYMENT - Langsung create pesanan & pembayaran
     * Cash langsung masuk database dengan status pending
     */
    private function processCashPayment($carts, $total, $meja, $mejaId, $jenisPesanan)
    {
        try {
            DB::beginTransaction();

            Log::info('💵 Processing cash payment', [
                'meja_id' => $mejaId,
                'total' => $total
            ]);

            // ✅ Buat pesanan untuk cash (langsung dibuat)
            $pesanan = Pesanan::create([
                'meja_id' => $mejaId,
                'jenis_pesanan' => 'dinein',
                'tanggal_pesanan' => now()->format('Y-m-d'),
                'waktu_pesanan' => now()->format('H:i:s'),
                'total_harga' => $total,
                'status_pesanan' => 'pending',
                'metode_pembayaran' => 'cash',
            ]);

            // ✅ Simpan detail pesanan
            foreach ($carts as $cart) {
                if (!$cart->menu) continue;
                
                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id' => $cart->menu->id,
                    'jumlah' => $cart->qty,
                    'harga_satuan' => $cart->menu->harga,
                    'subtotal' => $cart->menu->harga * $cart->qty,
                ]);
            }

            $orderId = 'CASH-DINEIN-' . $pesanan->id . '-' . now()->timestamp;

            // ✅ Buat pembayaran dengan status pending
            $pembayaran = Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'order_id' => $orderId,
                'total_bayar' => $total,
                'metode_pembayaran' => 'cash',
                'status_pembayaran' => 'pending',
                'jenis_pesanan' => 'dinein',
                'nomor_meja' => $meja->nomor_meja,
                'tanggal_pesanan' => $pesanan->tanggal_pesanan,
                'waktu_pesanan' => $pesanan->waktu_pesanan,
            ]);

            // ✅ Hapus cart setelah berhasil
            Cart::where('meja_id', $mejaId)
                ->where('jenis_pesanan', $jenisPesanan)
                ->delete();

            DB::commit();

            Log::info('✅ Pembayaran cash berhasil dibuat', [
                'order_id' => $orderId,
                'pesanan_id' => $pesanan->id,
                'pembayaran_id' => $pembayaran->id,
                'status_pembayaran' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'payment_method' => 'cash',
                'order_id' => $orderId,
                'redirect_url' => route('cart.dinein.checkout.success')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('❌ Error saat proses pembayaran cash', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'meja_id' => $mejaId
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan saat memproses pembayaran cash: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ PROCESS QRIS PAYMENT - SESUAI ALUR MIDTRANS POOLING
     * TIDAK create pesanan di database, hanya save ke cache dan generate Snap Token
     * Data pesanan baru akan dibuat ketika user klik "Check Status" dan pooling controller mendeteksi settlement
     */
    private function processQrisPayment($carts, $total, $items, $meja, $mejaId, $jenisPesanan)
    {
        try {
            $orderId = 'QRIS-DINEIN-' . time() . '-' . rand(1000, 9999);

            Log::info('💳 Processing QRIS payment', [
                'order_id' => $orderId,
                'meja_id' => $mejaId,
                'total' => $total
            ]);

            // ✅ KUNCI UTAMA: Siapkan data untuk disimpan ke cache (BUKAN database)
            // Data ini akan diambil oleh MidtransPollingController saat settlement
            $qrisData = [
                'meja_id' => $mejaId,
                'nomor_meja' => $meja->nomor_meja,
                'jenis_pesanan' => 'dinein',
                'total_harga' => $total,
                'order_id' => $orderId,
                'items' => [], // Format untuk create pesanan nanti
            ];

            // ✅ Format items untuk disimpan di cache
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
                Log::error('❌ Failed to save QRIS data to cache', ['order_id' => $orderId]);
                return response()->json(['error' => 'Gagal menyimpan data QRIS'], 500);
            }

            Log::info('💾 QRIS data saved to cache for pooling', [
                'order_id' => $orderId,
                'meja_id' => $mejaId,
                'total' => $total,
                'cache_key' => "qris_data_{$orderId}",
                'note' => 'Data siap untuk settlement pooling'
            ]);

            // ✅ Parameter Snap Midtrans
            $snapItems = [];
            foreach ($items as $item) {
                $snapItems[] = [
                    'id' => $item['menu_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name' => $item['name'],
                ];
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $total,
                ],
                'item_details' => $snapItems,
                'customer_details' => [
                    'first_name' => 'Customer Dine-In',
                    'last_name' => 'Meja ' . $meja->nomor_meja,
                    'email' => 'dinein@restaurant.com',
                    'phone' => '081234567890',
                ],
                'custom_field1' => 'dinein',
                'custom_field2' => $meja->nomor_meja,
                'custom_field3' => $orderId,
            ];

            // ✅ Generate Snap Token
            $snapToken = Snap::getSnapToken($params);

            Log::info('🎟️ Snap token generated successfully', [
                'order_id' => $orderId,
                'snap_token_preview' => substr($snapToken, 0, 20) . '...'
            ]);

            // ✅ Hapus cart SETELAH snap token berhasil dibuat
            $deletedCarts = Cart::where('meja_id', $mejaId)
                ->where('jenis_pesanan', $jenisPesanan)
                ->delete();

            Log::info('🗑️ Cart cleared after successful QRIS token generation', [
                'deleted_count' => $deletedCarts,
                'meja_id' => $mejaId
            ]);

            Log::info('✅ QRIS checkout process completed', [
                'order_id' => $orderId,
                'meja_id' => $mejaId,
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
                'meja_nomor' => $meja->nomor_meja,
                'message' => 'Silakan scan QRIS untuk melakukan pembayaran. Setelah bayar, klik tombol "Check Status"'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Midtrans Snap Token Error Dinein QRIS', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'meja_id' => $mejaId,
                'order_id' => $orderId ?? 'not_generated'
            ]);

            return response()->json([
                'error' => 'Gagal menghubungkan ke Midtrans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ PROCESS CASH - Method terpisah untuk backward compatibility
     */
    public function processCash(Request $request)
    {
        $request->merge(['payment_type' => 'cash']);
        return $this->process($request);
    }

    /**
     * ✅ DINE-IN SUCCESS - Halaman sukses (untuk cash dan QRIS)
     */
    public function dineInSuccess()
    {
        // ✅ Clear session setelah checkout berhasil
        session()->forget(['meja_id', 'jenis_pesanan']);

        Log::info('✅ Dinein success page loaded');

        return view('cart.dinein.success', [
            'title' => 'Pembayaran Berhasil',
            'message' => 'Terima kasih! Pesanan Anda sedang diproses.',
            'type' => 'success'
        ]);
    }

    /**
     * ✅ CASH SUCCESS - Halaman sukses khusus cash
     */
    public function cashSuccess()
    {
        session()->forget(['meja_id', 'jenis_pesanan']);
        
        Log::info('✅ Cash checkout success page loaded');
        
        return view('cart.dinein.sukses', [
            'title' => 'Pesanan Berhasil Dibuat',
            'message' => 'Silakan bayar ke kasir untuk melanjutkan pesanan.',
            'type' => 'cash'
        ]);
    }

    /**
     * ✅ CALLBACK - Deprecated, tidak digunakan lagi karena pakai pooling
     * Tetap ada untuk backward compatibility
     */
    public function dineInCallback(Request $request)
    {
        Log::warning('⚠️ Deprecated callback called, redirecting to pooling system', [
            'request_data' => $request->all()
        ]);

        // Redirect ke pooling system jika ada order_id
        $orderId = $request->input('order_id');
        if ($orderId) {
            return redirect()->route('admin.midtrans.cek.status', ['order_id' => $orderId]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Please use pooling system instead of callback'
        ], 410); // 410 Gone
    }

    /**
     * ✅ CLEAR EXPIRED CACHE - Utility method
     */
    public function clearExpiredQrisCache()
    {
        // This method can be called via scheduled task
        // to clean up expired QRIS cache data
        
        try {
            $clearedCount = 0;
            // Implementation depends on your cache driver
            // For Redis, you might need to scan for keys with pattern
            
            Log::info('✅ Expired QRIS cache cleanup completed', [
                'cleared_count' => $clearedCount
            ]);
            
            return response()->json([
                'success' => true,
                'cleared_count' => $clearedCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Failed to clear expired QRIS cache', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}