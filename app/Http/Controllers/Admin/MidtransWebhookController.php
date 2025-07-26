<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Models\Pembayaran;
use App\Models\Pesanan;

class MidtransWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $serverKey = Config::get('services.midtrans.server_key');
        $rawBody = $request->getContent();
        $data = json_decode($rawBody, true);

        Log::info('✅ Midtrans Webhook Received', $data);

        if (!isset($data['order_id'], $data['status_code'], $data['gross_amount'], $data['signature_key'])) {
            return response()->json(['message' => 'Invalid data'], 400);
        }

        // Validasi signature
        $expectedSignature = hash('sha512', $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $serverKey);
        if ($data['signature_key'] !== $expectedSignature) {
            Log::warning('❌ Invalid Midtrans Signature!');
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $orderId = $data['order_id'];
        $transactionStatus = $data['transaction_status'] ?? null;
        $grossAmount = $data['gross_amount'];
        $paymentType = $data['payment_type'] ?? 'qris';

        // Mapping status Midtrans ke status lokal
        $statusLokal = match ($transactionStatus) {
            'settlement', 'capture' => 'dibayar',
            'pending' => 'pending',
            'cancel', 'deny', 'expire', 'failure' => 'gagal',
            default => 'pending',
        };

        // Cari atau buat pembayaran berdasarkan order_id
        $pembayaran = Pembayaran::where('order_id', $orderId)->first();

        if (!$pembayaran) {
            // Deteksi pesanan_id dari order_id (PESANAN-123-xxxx atau ORDER-123-xxxx)
            $pesananId = null;

            if (preg_match('/(?:PESANAN|ORDER)-(\d+)-/', $orderId, $matches)) {
                $pesananId = (int)$matches[1];
            }

            $pesanan = Pesanan::find($pesananId);

            $pembayaran = Pembayaran::create([
                'order_id'           => $orderId,
                'pesanan_id'         => $pesanan?->id,
                'total_bayar'        => $grossAmount,
                'metode_pembayaran'  => $paymentType,
                'status_pembayaran'  => $statusLokal,
                'jenis_pesanan'      => $pesanan?->jenis_pesanan,
                'nomor_meja' => $pesanan?->meja?->nomor_meja,
                'nama_pelanggan'     => $pesanan?->nama_pelanggan,
                'nomor_wa'           => $pesanan?->nomor_wa,
            ]);

            Log::info("✅ Pembayaran baru dibuat untuk order_id: $orderId");
        } else {
            // Update status pembayaran jika sudah ada
            $pembayaran->status_pembayaran = $statusLokal;
            $pembayaran->save();

            Log::info("✅ Status pembayaran order_id $orderId diupdate ke: $statusLokal");
        }

        // Update status pesanan jika ada
        if ($pembayaran->pesanan) {
            $statusPesanan = match ($statusLokal) {
                'dibayar' => 'dibayar',
                'gagal' => 'dibatalkan',
                default => 'pending',
            };

            $pembayaran->pesanan->update([
                'status_pesanan' => $statusPesanan,
            ]);

            Log::info("✅ Status pesanan ID {$pembayaran->pesanan->id} diupdate ke: $statusPesanan");
        }

        return response()->json(['message' => 'Webhook processed successfully']);
    }
}
