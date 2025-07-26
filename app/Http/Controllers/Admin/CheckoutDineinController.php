<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pembayaran;
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

    public function index()
    {
        $pembayarans = Pembayaran::with('pesanan')->latest()->paginate(10);
        return view('admin.pages.pembayaran.index', compact('pembayarans'));
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::with('pesanan.detail')->findOrFail($id);
        return view('admin.pages.pembayaran.show', compact('pembayaran'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status_pembayaran' => 'required|in:pending,dibayar,dibatalkan',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->update(['status_pembayaran' => $validated['status_pembayaran']]);

        if ($pembayaran->pesanan) {
            $statusPesanan = $validated['status_pembayaran'] === 'dibayar' ? 'dibayar' : 'batal';
            $pembayaran->pesanan->update(['status_pesanan' => $statusPesanan]);
        }

        return redirect()->route('admin.pembayaran.index')->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        if ($pembayaran->pesanan) {
            $pembayaran->pesanan->delete();
        }
        $pembayaran->delete();

        return redirect()->route('admin.pembayaran.index')->with('success', 'Data pembayaran berhasil dihapus.');
    }

    public function process(Request $request)
    {
        $mejaId = session('meja_id');
        $jenisPesanan = session('jenis_pesanan');
        $paymentType = $request->input('payment_type');

        if (!$mejaId || $jenisPesanan !== 'dinein') {
            return response()->json(['error' => 'Sesi meja atau jenis pesanan tidak valid.'], 400);
        }

        $carts = Cart::with('menu')
            ->where('meja_id', $mejaId)
            ->where('jenis_pesanan', $jenisPesanan)
            ->get();

        if ($carts->isEmpty()) {
            return response()->json(['error' => 'Keranjang Dine-In kosong!'], 400);
        }

        $total = 0;
        $items = [];

        foreach ($carts as $cart) {
            if (!$cart->menu) continue;

            $subtotal = $cart->menu->harga * $cart->qty;
            $total += $subtotal;

            $items[] = [
                'id' => $cart->menu->id,
                'price' => $cart->menu->harga,
                'quantity' => $cart->qty,
                'name' => $cart->menu->nama_menu,
            ];
        }

        $pesanan = Pesanan::create([
            'user_id' => null,
            'meja_id' => $mejaId,
            'tanggal_pesanan' => now()->format('Y-m-d'),
            'waktu_pesanan' => now()->format('H:i:s'),
            'total_harga' => $total,
            'status_pesanan' => 'pending',
            'jenis_pesanan' => $jenisPesanan,
        ]);

        foreach ($carts as $cart) {
            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'menu_id' => $cart->menu->id,
                'jumlah' => $cart->qty,
                'subtotal' => $cart->menu->harga * $cart->qty,
            ]);
        }

        if ($paymentType === 'cash') {
            Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'user_id' => null,
                'total_bayar' => $total,
                'metode_pembayaran' => 'cash',
                'status_pembayaran' => 'pending',
            ]);

            return response()->json(['success' => true]);
        }

        $orderId = 'PESANAN-' . $pesanan->id . '-' . now()->timestamp;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => 'Customer',
                'email' => 'dinein@gmail.com',
                'phone' => '081234567890',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghubungkan ke Midtrans: ' . $e->getMessage()], 500);
        }
    }

    public function processCash(Request $request)
{
    $mejaId = session('meja_id');
    $jenisPesanan = session('jenis_pesanan');

    if (!$mejaId || $jenisPesanan !== 'dinein') {
        return response()->json(['error' => 'Sesi meja atau jenis pesanan tidak valid.'], 400);
    }

    $carts = Cart::with('menu')
        ->where('meja_id', $mejaId)
        ->where('jenis_pesanan', $jenisPesanan)
        ->get();

    if ($carts->isEmpty()) {
        return response()->json(['error' => 'Keranjang Dine-In kosong!'], 400);
    }

    $total = 0;
    foreach ($carts as $cart) {
        if ($cart->menu) {
            $total += $cart->menu->harga * $cart->qty;
        }
    }

    // Simpan ke pesanan
    $pesanan = Pesanan::create([
        'user_id' => null,
        'meja_id' => $mejaId,
        'tanggal_pesanan' => now()->format('Y-m-d'),
        'waktu_pesanan' => now()->format('H:i:s'),
        'total_harga' => $total,
        'status_pesanan' => 'pending',
        'jenis_pesanan' => $jenisPesanan,
    ]);

    foreach ($carts as $cart) {
        DetailPesanan::create([
            'pesanan_id' => $pesanan->id,
            'menu_id' => $cart->menu_id,
            'jumlah' => $cart->qty,
            'subtotal' => $cart->menu->harga * $cart->qty,
        ]);
    }

    // ✅ Buat order_id unik
    $orderId = 'CASH-' . $pesanan->id . '-' . time();

    // Simpan pembayaran cash
    Pembayaran::create([
        'pesanan_id' => $pesanan->id,
        'order_id' => $orderId,
        'user_id' => null,
        'total_bayar' => $total,
        'metode_pembayaran' => 'cash',
        'status_pembayaran' => 'pending',
        'jenis_pesanan' => $jenisPesanan,
        'meja_id' => $mejaId,
    ]);

    // Hapus cart
    Cart::where('meja_id', $mejaId)
        ->where('jenis_pesanan', $jenisPesanan)
        ->delete();

    // Redirect ke halaman sukses
    return response()->json([
        'success' => true,
        'redirect_url' => route('cart.dinein.checkout.sukses'),
    ]);
}

        public function cashSuccess()
    {
        session()->forget('meja_id');
        session()->forget('jenis_pesanan');

        return view('cart.dinein.sukses'); // Pastikan file `resources/views/cart/dinein/success.blade.php` ada
    }

    public function dineInSuccess()
    {
        session()->forget('meja_id');
        session()->forget('jenis_pesanan');

        return view('cart.dinein.success');
    }

    public function callback(Request $request)
    {
        Log::info('Midtrans Callback Diterima', $request->all());

        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", "{$request->order_id}{$request->status_code}{$request->gross_amount}{$serverKey}");

        if ($hashed !== $request->signature_key) {
            Log::warning('Signature Key tidak cocok!', ['order_id' => $request->order_id]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        preg_match('/PESANAN-(\d+)-/', $request->order_id, $matches);
        $pesananId = $matches[1] ?? null;

        if (!$pesananId) {
            Log::warning('Gagal parse order_id dari Midtrans', ['order_id' => $request->order_id]);
            return response()->json(['error' => 'Invalid order_id'], 400);
        }

        $pesanan = Pesanan::find($pesananId);

        if (!$pesanan) {
            Log::warning('Pesanan tidak ditemukan!', ['pesanan_id' => $pesananId]);
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }

        $status = $request->transaction_status;

        if (in_array($status, ['capture', 'settlement'])) {
            $pesanan->update(['status_pesanan' => 'dibayar']);

            Pembayaran::updateOrCreate(
                ['order_id' => $request->order_id],
                [
                    'pesanan_id' => $pesanan->id,
                    'total_bayar' => $request->gross_amount,
                    'metode_pembayaran' => $request->payment_type,
                    'status_pembayaran' => 'dibayar',
                    'jenis_pesanan' => $pesanan->jenis_pesanan,
                    'meja_id' => $pesanan->meja_id,
                ]
            );

            Cart::where('meja_id', $pesanan->meja_id)
                ->where('jenis_pesanan', $pesanan->jenis_pesanan)
                ->delete();

            Log::info('Pembayaran berhasil diproses', ['pesanan_id' => $pesanan->id]);
        } elseif (in_array($status, ['cancel', 'expire', 'failure'])) {
            $pesanan->update(['status_pesanan' => 'batal']);

            Pembayaran::updateOrCreate(
                ['order_id' => $request->order_id],
                [
                    'pesanan_id' => $pesanan->id,
                    'total_bayar' => $request->gross_amount,
                    'metode_pembayaran' => $request->payment_type,
                    'status_pembayaran' => 'dibatalkan',
                    'jenis_pesanan' => $pesanan->jenis_pesanan,
                    'meja_id' => $pesanan->meja_id,
                ]
            );

            Log::info('Pembayaran dibatalkan', ['pesanan_id' => $pesanan->id]);
        }

        return response()->json(['message' => 'Callback processed']);
    }
}