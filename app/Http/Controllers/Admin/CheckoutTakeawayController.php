<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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

    public function process(Request $request)
    {
        $nomorWa = Session::get('takeaway.nomor_wa');
        $namaPelanggan = Session::get('takeaway.nama_pelanggan') ?? 'Pelanggan Takeaway';

        if (!$nomorWa) {
            return response()->json(['error' => 'Data pelanggan tidak ditemukan.'], 400);
        }

        $carts = Cart::with('menu')
            ->where('nomor_wa', $nomorWa)
            ->where('jenis_pesanan', 'takeaway')
            ->get();

        if ($carts->isEmpty()) {
            return response()->json(['error' => 'Keranjang Takeaway kosong!'], 400);
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
            'jenis_pesanan'   => 'takeaway',
            'nama_pelanggan'  => $namaPelanggan,
            'nomor_wa'        => $nomorWa,
            'tanggal_pesanan' => Session::get('takeaway.tanggal_pesanan'),
            'waktu_pesanan'   => Session::get('takeaway.waktu_pesanan'),
            'total_harga'     => $total,
            'status_pesanan'  => 'pending',
        ]);

        foreach ($carts as $cart) {
            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'menu_id'    => $cart->menu->id,
                'jumlah'     => $cart->qty,
                'subtotal'   => $cart->menu->harga * $cart->qty,
            ]);
        }

        // Generate Snap Token (QRIS)
        $params = [
            'transaction_details' => [
                'order_id' => 'PESANAN-' . $pesanan->id . '-' . now()->timestamp,
                'gross_amount' => $total,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $namaPelanggan,
                // 'email' => 'takeaway@email.com',
                'phone' => $nomorWa,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error Takeaway: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghubungkan ke Midtrans: ' . $e->getMessage()], 500);
        }
    }

   public function takeawaySuccess()
 {
    $nama_pelanggan  = Session::get('takeaway.nama_pelanggan');
    $nomor_wa        = Session::get('takeaway.nomor_wa');
    $tanggal_pesanan = Session::get('takeaway.tanggal_pesanan');
    $waktu_pesanan   = Session::get('takeaway.waktu_pesanan');

    // Hapus session setelah datanya disimpan ke variabel
    Session::forget('takeaway');

    return view('cart.takeaway.success', compact(
        'nama_pelanggan',
        'nomor_wa',
        'tanggal_pesanan',
        'waktu_pesanan'
    ));
 }


    public function takeawayCallback(Request $request)
    {
        Log::info('Midtrans Callback Takeaway', $request->all());

        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", "{$request->order_id}{$request->status_code}{$request->gross_amount}{$serverKey}");

        if ($hashed !== $request->signature_key) {
            Log::warning('Signature Key tidak cocok!');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $orderId = str_replace('PESANAN-', '', $request->order_id);
        $orderIdParts = explode('-', $orderId);
        $pesananId = $orderIdParts[0] ?? null;

        $pesanan = Pesanan::find($pesananId);
        if (!$pesanan) {
            Log::warning('Pesanan tidak ditemukan!', ['pesanan_id' => $request->order_id]);
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }

        if (in_array($request->transaction_status, ['capture', 'settlement'])) {
            $pesanan->update(['status_pesanan' => 'dibayar']);

            Pembayaran::updateOrCreate(
                ['pesanan_id' => $pesanan->id], 
                [
                    'user_id' => null,
                    'total_bayar' => $request->gross_amount,
                    'metode_pembayaran' => $request->payment_type,
                    'status_pembayaran' => 'dibayar',
                ]
            );

            Cart::where('nomor_wa', $pesanan->nomor_wa)
                ->where('jenis_pesanan', $pesanan->jenis_pesanan)
                ->delete();

            Log::info('Pembayaran Takeaway berhasil diproses', ['pesanan_id' => $pesanan->id]);
        } elseif (in_array($request->transaction_status, ['cancel', 'failure', 'expire'])) {
            $pesanan->update(['status_pesanan' => 'batal']);

            Pembayaran::updateOrCreate(
                ['pesanan_id' => $pesanan->id],
                [
                    'user_id' => null,
                    'total_bayar' => $request->gross_amount,
                    'metode_pembayaran' => $request->payment_type,
                    'status_pembayaran' => 'dibatalkan',
                ]
            );

            Log::info('Pembayaran Takeaway dibatalkan', ['pesanan_id' => $pesanan->id]);
        }

        return response()->json(['message' => 'Callback Takeaway processed']);
    }
}
