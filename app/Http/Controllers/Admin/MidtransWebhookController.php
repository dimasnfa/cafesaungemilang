?php

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use App\Models\Pembayaran;
// use App\Models\Pesanan;

// class MidtransWebhookController extends Controller
// {
//     public function handleWebhook(Request $request)
//     {
//         $serverKey = config('services.midtrans.server_key');
//         $rawBody = $request->getContent();
//         $data = json_decode($rawBody, true);

//         Log::info('📥 Incoming Midtrans Webhook:', $data);

//         // Cek apakah data yang diperlukan tersedia
//         if (!$data || !isset($data['order_id'])) {
//             Log::warning('❌ Webhook tanpa order_id!');
//             return response()->json(['message' => 'Invalid payload'], 400);
//         }

//         // [🧪 Nonaktifkan Signature Validasi SEMENTARA]
//         // $expectedSignature = hash('sha512', $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $serverKey);
//         // if ($data['signature_key'] !== $expectedSignature) {
//         //     Log::warning('❌ Invalid Midtrans Signature!');
//         //     return response()->json(['message' => 'Unauthorized'], 403);
//         // }

//         // Ambil ID pesanan dari order_id (format: PESANAN-<id>-<timestamp>)
//         $orderParts = explode('-', $data['order_id']);
//         $pesananId = isset($orderParts[1]) ? $orderParts[1] : null;

//         if (!$pesananId) {
//             Log::error('❌ Tidak bisa parsing order_id: ' . $data['order_id']);
//             return response()->json(['message' => 'Invalid order ID'], 400);
//         }

//         $pesanan = Pesanan::with('meja')->find($pesananId);

//         if (!$pesanan) {
//             Log::error('❌ Pesanan tidak ditemukan: ID ' . $pesananId);
//             return response()->json(['message' => 'Pesanan not found'], 404);
//         }

//         // Cek apakah sudah pernah dibayar
//         if (Pembayaran::where('order_id', $data['order_id'])->exists()) {
//             Log::info('✅ Pembayaran sudah tercatat sebelumnya: ' . $data['order_id']);
//             return response()->json(['message' => 'Already processed'], 200);
//         }

//         // Simpan pembayaran
//         Pembayaran::create([
//             'order_id' => $data['order_id'],
//             'pesanan_id' => $pesanan->id,
//             'total_bayar' => $data['gross_amount'],
//             'metode_pembayaran' => $data['payment_type'] ?? 'midtrans',
//             'status_pembayaran' => $data['transaction_status'],
//             'jenis_pesanan' => $pesanan->jenis_pesanan,
//             'nama_pelanggan' => $pesanan->nama_pelanggan ?? $pesanan->meja?->nomor_meja,
//             'nomor_wa' => $pesanan->nomor_wa ?? '-',
//         ]);

//         Log::info('✅ Pembayaran berhasil disimpan: ' . $data['order_id']);
//         return response()->json(['message' => 'Success'], 200);
//     }
// }
