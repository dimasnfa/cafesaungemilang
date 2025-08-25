<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Kategori;
use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PesananController extends Controller
{
    /**
     * ✅ INDEX - Sesuai alur Midtrans Pooling
     */
    public function index(Request $request)
    {
        $query = Pesanan::with(['detailPesanan.menu', 'meja', 'pembayaran'])
                        ->orderBy('created_at', 'desc');

        // Filter berdasarkan jenis pesanan
        if ($request->has('jenis') && in_array($request->jenis, ['dinein', 'takeaway'])) {
            $query->where('jenis_pesanan', $request->jenis);
        }

        // Filter berdasarkan status pesanan
        if ($request->has('status') && in_array($request->status, ['pending', 'dibayar', 'selesai', 'dibatalkan'])) {
            $query->where('status_pesanan', $request->status);
        }

        // Filter berdasarkan metode pembayaran
        if ($request->has('metode') && in_array($request->metode, ['cash', 'qris'])) {
            $query->where('metode_pembayaran', $request->metode);
        }

        // Get data dulu baru hitung statistik
        $pesanans = $query->get();

        // ✅ STATISTIK SESUAI ALUR POOLING: Prioritas QRIS yang sudah settlement
        $statistics = [
            'total_pesanan' => $pesanans->count(),
            'pending_pesanan' => $pesanans->where('status_pesanan', 'pending')->count(),
            
            // ✅ PRIORITAS UTAMA: QRIS yang sudah settlement tapi belum dikonfirmasi kasir
            'qris_ready_confirmation' => $pesanans->where('metode_pembayaran', 'qris')
                                                  ->where('status_pesanan', 'pending')
                                                  ->filter(function($pesanan) {
                                                      return $pesanan->pembayaran && 
                                                             $pesanan->pembayaran->status_pembayaran === 'dibayar';
                                                  })->count(),
                                                  
            // Cash yang masih pending (belum dikonfirmasi)
            'cash_pending' => $pesanans->where('metode_pembayaran', 'cash')
                                      ->where('status_pesanan', 'pending')->count(),
                                      
            // ✅ TAMBAHAN: QRIS yang belum settlement (masih di cache/waiting)
            'qris_waiting_settlement' => $pesanans->where('metode_pembayaran', 'qris')
                                                  ->where('status_pesanan', 'pending')
                                                  ->filter(function($pesanan) {
                                                      return !$pesanan->pembayaran || 
                                                             $pesanan->pembayaran->status_pembayaran !== 'dibayar';
                                                  })->count(),
        ];

        Log::info('Pesanan Index - Data loaded with pooling statistics', [
            'filter_jenis' => $request->jenis,
            'filter_status' => $request->status,
            'filter_metode' => $request->metode,
            'statistics' => $statistics
        ]);

        return view('admin.pages.pesanan.index', compact('pesanans', 'statistics'));
    }

    public function create()
    {
        $menus = Menu::where('stok', '>', 0)->get();
        $kategoris = Kategori::all();
        $mejas = Meja::where('status_meja', 'tersedia')->get();

        return view('admin.pages.pesanan.create', compact('menus', 'kategoris', 'mejas'));
    }

    public function edit($id)
    {
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu', 'pembayaran'])->findOrFail($id);
        $menus = Menu::all();
        $mejas = Meja::all();
        $kategoris = Kategori::all();

        return view('admin.pages.pesanan.edit', compact('pesanan', 'menus', 'mejas', 'kategoris'));
    }

    /**
     * ✅ UPDATE - SESUAI ALUR MIDTRANS POOLING
     * Validasi khusus untuk QRIS yang harus sudah settlement sebelum bisa dikonfirmasi
     */
    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::with('pembayaran')->findOrFail($id);
        $oldStatus = $pesanan->status_pesanan;

        $request->validate([
            'status_pesanan' => 'required|in:pending,dibayar,selesai,dibatalkan',
        ]);

        Log::info('Update status pesanan dimulai - Midtrans Pooling Flow', [
            'pesanan_id' => $pesanan->id,
            'old_status' => $oldStatus,
            'new_status' => $request->status_pesanan,
            'metode_pembayaran' => $pesanan->metode_pembayaran,
            'current_payment_status' => $pesanan->pembayaran?->status_pembayaran,
            'midtrans_order_id' => $pesanan->midtrans_order_id
        ]);

        // ✅ VALIDASI KUNCI MIDTRANS POOLING: QRIS harus settlement dulu
        if ($pesanan->metode_pembayaran === 'qris') {
            
            // Jika ingin ubah status pesanan ke "dibayar" (konfirmasi kasir)
            if ($request->status_pesanan === 'dibayar') {
                
                // ✅ CEK SETTLEMENT STATUS dari pooling
                if (!$pesanan->pembayaran || $pesanan->pembayaran->status_pembayaran !== 'dibayar') {
                    
                    Log::warning('❌ MIDTRANS POOLING: Attempt to confirm QRIS order but not settled yet', [
                        'pesanan_id' => $pesanan->id,
                        'payment_status' => $pesanan->pembayaran?->status_pembayaran ?? 'no_payment_record',
                        'settlement_time' => $pesanan->pembayaran?->settlement_time ?? 'null',
                        'note' => 'User belum klik Check Status atau belum settlement di Midtrans'
                    ]);

                    return redirect()->back()
                                    ->with('error', '❌ TIDAK DAPAT KONFIRMASI PESANAN QRIS!<br><br>' .
                                                   '🔄 <strong>Alasan:</strong> Pembayaran QRIS belum ter-settlement.<br>' .
                                                   '📱 <strong>Solusi:</strong> Pastikan customer sudah:<br>' .
                                                   '&nbsp;&nbsp;1. Melakukan pembayaran QRIS di Midtrans<br>' .
                                                   '&nbsp;&nbsp;2. Mengklik tombol "Check Status" di halaman pembayaran<br><br>' .
                                                   '⏰ Jika customer sudah bayar dan klik "Check Status", pesanan akan otomatis muncul dan siap dikonfirmasi.');
                }

                // ✅ Settlement sudah berhasil via pooling
                Log::info('✅ MIDTRANS POOLING: QRIS order confirmed after settlement', [
                    'pesanan_id' => $pesanan->id,
                    'settlement_time' => $pesanan->pembayaran->settlement_time,
                    'midtrans_order_id' => $pesanan->midtrans_order_id,
                    'pooling_flow' => 'success'
                ]);
            }

            // Untuk status lain (selesai, dibatalkan) tetap boleh diubah kasir
        }

        // ✅ LOGIC CASH: Tidak ada validasi khusus, kasir bebas ubah status
        if ($pesanan->metode_pembayaran === 'cash') {
            Log::info('💰 Cash order status update by cashier', [
                'pesanan_id' => $pesanan->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status_pesanan
            ]);
        }

        // Update status pesanan
        $pesanan->status_pesanan = $request->status_pesanan;
        $pesanan->save();

        Log::info('✅ Status pesanan berhasil diupdate - Midtrans Pooling', [
            'pesanan_id' => $pesanan->id,
            'old_status' => $oldStatus,
            'new_status' => $request->status_pesanan,
            'metode_pembayaran' => $pesanan->metode_pembayaran,
            'flow' => 'pooling_controlled'
        ]);

        // ✅ Pesan sukses yang informatif untuk pooling flow
        $message = 'Status pesanan berhasil diperbarui';
        
        if ($pesanan->metode_pembayaran === 'qris' && $request->status_pesanan === 'dibayar') {
            $message .= '. ✅ Pesanan QRIS dikonfirmasi setelah pembayaran ter-settlement via Midtrans Pooling.';
        } elseif ($pesanan->metode_pembayaran === 'cash' && 
                  $request->status_pesanan === 'dibayar' &&
                  $oldStatus === 'pending') {
            $message .= ' dan status pembayaran cash otomatis diperbarui.';
        }

        return redirect()->route('admin.pesanan.index')->with('success', $message);
    }

    public function destroy($id)
    {
        $pesanan = Pesanan::with(['pembayaran', 'detailPesanan'])->findOrFail($id);

        // ✅ Validasi tidak bisa hapus pesanan yang sudah dibayar
        if ($pesanan->status_pesanan === 'dibayar') {
            return redirect()->route('admin.pesanan.index')
                            ->with('error', '❌ Tidak dapat menghapus pesanan yang sudah dibayar.');
        }

        // ✅ VALIDASI KHUSUS MIDTRANS POOLING: QRIS yang sudah settlement
        if ($pesanan->metode_pembayaran === 'qris' && 
            $pesanan->pembayaran && 
            $pesanan->pembayaran->status_pembayaran === 'dibayar') {
            
            return redirect()->route('admin.pesanan.index')
                            ->with('error', '❌ Tidak dapat menghapus pesanan QRIS yang sudah ter-settlement via Midtrans Pooling.');
        }

        Log::info('🗑️ Pesanan dihapus - Midtrans Pooling Flow', [
            'pesanan_id' => $pesanan->id,
            'status' => $pesanan->status_pesanan,
            'metode_pembayaran' => $pesanan->metode_pembayaran,
            'payment_status' => $pesanan->pembayaran?->status_pembayaran,
            'midtrans_order_id' => $pesanan->midtrans_order_id
        ]);

        $pesanan->delete();

        return redirect()->route('admin.pesanan.index')->with('success', '✅ Pesanan berhasil dihapus.');
    }

    /**
     * ✅ SHOW - Tambahan info pooling status
     */
    public function show($id)
    {
        $pesanan = Pesanan::with(['menu', 'meja', 'pembayaran', 'detailPesanan.menu'])->findOrFail($id);
        
        // ✅ Info pooling untuk monitoring
        $paymentInfo = null;
        if ($pesanan->pembayaran) {
            $paymentInfo = [
                'is_qris_settled' => $pesanan->pembayaran->status_pembayaran === 'dibayar',
                'ready_for_confirmation' => $pesanan->metode_pembayaran === 'qris' && 
                                          $pesanan->pembayaran->status_pembayaran === 'dibayar' && 
                                          $pesanan->status_pesanan === 'pending',
                'settlement_time' => $pesanan->pembayaran->settlement_time,
                'order_id' => $pesanan->pembayaran->order_id,
                'pooling_status' => $pesanan->metode_pembayaran === 'qris' ? 
                    ($pesanan->pembayaran->status_pembayaran === 'dibayar' ? 'settled_via_pooling' : 'waiting_settlement') : 
                    'not_applicable'
            ];
        }
        
        return view('admin.pages.pesanan.show', compact('pesanan', 'paymentInfo'));
    }

    /**
     * ✅ STORE - Updated untuk handle metode pembayaran sesuai pooling
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_pesanan' => 'required|in:dinein,takeaway',
            'menu_id' => 'required|array',
            'menu_id.*' => 'exists:menu,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
            'tanggal_pesanan' => 'required|date',
            'waktu_pesanan' => 'required',
            'metode_pembayaran' => 'required|in:cash,qris',
        ]);

        $jenisPesanan = $request->jenis_pesanan;
        $data = [
            'tanggal_pesanan' => $request->tanggal_pesanan,
            'waktu_pesanan' => $request->waktu_pesanan,
            'total_harga' => 0,
            'status_pesanan' => 'pending',
            'jenis_pesanan' => $jenisPesanan,
            'metode_pembayaran' => $request->metode_pembayaran,
        ];

        if ($jenisPesanan === 'dinein') {
            $request->validate([
                'meja_id' => 'required|exists:meja,id',
            ]);
            $data['meja_id'] = $request->meja_id;
        } elseif ($jenisPesanan === 'takeaway') {
            $request->validate([
                'nama_pelanggan' => 'required|string|max:255',
                'nomor_wa' => 'required|string|max:20',
            ]);
            $data['nama_pelanggan'] = $request->nama_pelanggan;
            $data['nomor_wa'] = $request->nomor_wa;
            $data['meja_id'] = null;
        }

        // ✅ Untuk QRIS manual dari admin, set midtrans_order_id
        if ($request->metode_pembayaran === 'qris') {
            $data['midtrans_order_id'] = 'ADMIN-QRIS-' . time() . '-' . rand(1000, 9999);
        }

        // Simpan pesanan utama
        $pesanan = Pesanan::create($data);

        // Simpan detail pesanan
        $totalHarga = 0;
        foreach ($request->menu_id as $index => $menuId) {
            $menu = Menu::findOrFail($menuId);
            $jumlah = (int) $request->jumlah[$index];

            // Validasi stok
            if ($jumlah > $menu->stok) {
                return redirect()->back()->withErrors([
                    'jumlah' => 'Jumlah untuk menu ' . $menu->nama_menu . ' melebihi stok yang tersedia.'
                ]);
            }

            $subtotal = $menu->harga * $jumlah;

            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'menu_id' => $menuId,
                'jumlah' => $jumlah,
                'harga_satuan' => $menu->harga,
                'subtotal' => $subtotal,
            ]);

            $totalHarga += $subtotal;
        }

        // Update total harga pesanan
        $pesanan->update(['total_harga' => $totalHarga]);

        // ✅ Create pembayaran record untuk admin-created orders
        $this->createPaymentRecordForAdminOrder($pesanan, $request);

        Log::info('✅ Pesanan baru berhasil dibuat via admin - Midtrans Pooling Ready', [
            'pesanan_id' => $pesanan->id,
            'jenis_pesanan' => $jenisPesanan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'total_harga' => $totalHarga,
            'created_by' => 'admin',
            'midtrans_order_id' => $pesanan->midtrans_order_id
        ]);

        return redirect()->route('admin.pesanan.index')->with('success', '✅ Pesanan berhasil dibuat.');
    }

    /**
     * ✅ Create payment record untuk pesanan yang dibuat admin
     */
    private function createPaymentRecordForAdminOrder($pesanan, $request)
    {
        try {
            $orderIdPrefix = $request->metode_pembayaran === 'cash' ? 'ADMIN-CASH-' : 'ADMIN-QRIS-';
            $orderId = $orderIdPrefix . $pesanan->id . '-' . time();

            $paymentData = [
                'pesanan_id' => $pesanan->id,
                'order_id' => $orderId,
                'total_bayar' => $pesanan->total_harga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => 'pending', // Akan diupdate sesuai flow pooling
                'jenis_pesanan' => $pesanan->jenis_pesanan,
                'nama_pelanggan' => $pesanan->nama_pelanggan,
                'nomor_wa' => $pesanan->nomor_wa,
                'nomor_meja' => $pesanan->meja?->nomor_meja,
                'tanggal_pesanan' => $pesanan->tanggal_pesanan,
                'waktu_pesanan' => $pesanan->waktu_pesanan,
            ];

            $pesanan->pembayaran()->create($paymentData);

            Log::info('✅ Payment record created for admin order - Pooling Ready', [
                'pesanan_id' => $pesanan->id,
                'order_id' => $orderId,
                'metode' => $request->metode_pembayaran
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Failed to create payment record for admin order', [
                'pesanan_id' => $pesanan->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function showBookingPage($jenis)
    {
        $kategoris = Kategori::with('menus')->get();

        if ($kategoris->isEmpty()) {
            return "Tidak ada kategori dengan menu.";
        }

        session(['jenis_pesanan' => $jenis]);

        return view("cart.$jenis.booking", compact('kategoris'));
    }

    public function processQR(Request $request)
    {
        $mejaId = $request->input('meja_id');

        if (!$mejaId) {
            return redirect()->route('home')->with('error', 'QR Code tidak valid!');
        }

        return redirect()->route('dinein.booking', ['meja' => $mejaId]);
    }

    public function showCustomerForm()
    {
        return view('cart.takeaway.customer-form');
    }

    /**
     * ✅ CHECK NEW PESANAN - SESUAI ALUR MIDTRANS POOLING
     * Prioritas tinggi untuk QRIS yang baru settlement via pooling
     */
    public function checkNewPesanan(Request $request)
    {
        try {
            $jenis = $request->query('jenis');
            
            Log::info("🔍 Checking new orders - Midtrans Pooling Priority - Jenis: " . ($jenis ?? 'all'));

            // ✅ PRIORITAS TERTINGGI: QRIS yang baru settlement dalam 60 detik via pooling
            $qrisSettlementQuery = Pesanan::with(['meja', 'detailPesanan.menu', 'pembayaran'])
                   ->where('metode_pembayaran', 'qris')
                   ->where('status_pesanan', 'pending')
                   ->whereHas('pembayaran', function($q) {
                       $q->where('status_pembayaran', 'dibayar')
                         ->where('settlement_time', '>=', now()->subSeconds(60));
                   })
                   ->orderBy('created_at', 'desc');

            // Filter jenis pesanan
            if ($jenis) {
                $jenisNormalized = strtolower(str_replace('-', '', $jenis));
                if (in_array($jenisNormalized, ['dinein', 'takeaway'])) {
                    $qrisSettlementQuery->where('jenis_pesanan', $jenisNormalized);
                }
            }

            $priorityPesanan = $qrisSettlementQuery->first();

            // Jika ada QRIS settlement, prioritaskan dengan alert khusus
            if ($priorityPesanan) {
                return $this->formatPesananResponse($priorityPesanan, $jenis, 'qris_pooling_settlement');
            }

            // Jika tidak ada QRIS settlement, cek pesanan biasa dalam 30 detik
            $query = Pesanan::with(['meja', 'detailPesanan.menu', 'pembayaran'])
                   ->where('created_at', '>=', now()->subSeconds(30))
                   ->orderBy('created_at', 'desc');

            if ($jenis) {
                $jenisNormalized = strtolower(str_replace('-', '', $jenis));
                if (in_array($jenisNormalized, ['dinein', 'takeaway'])) {
                    $query->where('jenis_pesanan', $jenisNormalized);
                }
            }

            $latestPesanan = $query->first();

            if (!$latestPesanan) {
                return response()->json([
                    'new_pesanan' => false,
                    'message' => 'Tidak ada pesanan baru',
                    'timestamp' => now()->timestamp,
                    'pooling_status' => 'no_new_orders'
                ], 200);
            }

            // Check session notification
            $sessionKey = 'last_notified_pesanan_' . ($jenis ?? 'all');
            $lastNotifiedId = session($sessionKey, 0);

            if ($latestPesanan->id > $lastNotifiedId) {
                return $this->formatPesananResponse($latestPesanan, $jenis, 'regular');
            }

            return response()->json([
                'new_pesanan' => false,
                'message' => 'Tidak ada pesanan baru yang perlu dinotifikasi',
                'timestamp' => now()->timestamp,
                'pooling_status' => 'no_notification_needed'
            ], 200);

        } catch (\Exception $e) {
            Log::error('❌ Error checking new orders - Midtrans Pooling: ' . $e->getMessage());
            
            return response()->json([
                'new_pesanan' => false,
                'error' => true,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'timestamp' => now()->timestamp,
                'pooling_status' => 'error'
            ], 500);
        }
    }

    /**
     * ✅ FORMAT RESPONSE - Khusus untuk pooling notifications
     */
    private function formatPesananResponse($pesanan, $jenis, $type = 'regular')
    {
        $sessionKey = 'last_notified_pesanan_' . ($jenis ?? 'all');
        session([$sessionKey => $pesanan->id]);
        
        $jenisPesananNormalized = strtolower(str_replace('-', '', $pesanan->jenis_pesanan));
        
        $pesananData = [
            'id' => $pesanan->id,
            'jenis_pesanan' => $jenisPesananNormalized,
            'metode_pembayaran' => $pesanan->metode_pembayaran,
            'total_harga' => $pesanan->total_harga,
            'tanggal_pesanan' => $pesanan->tanggal_pesanan,
            'waktu_pesanan' => $pesanan->waktu_pesanan,
            'status_pesanan' => $pesanan->status_pesanan,
            'created_at' => $pesanan->created_at->format('Y-m-d H:i:s'),
            'created_timestamp' => $pesanan->created_at->timestamp,
            'status_pembayaran' => $pesanan->pembayaran?->status_pembayaran ?? 'belum_ada',
            'notification_type' => $type,
            'midtrans_order_id' => $pesanan->midtrans_order_id,
        ];

        // ✅ INFO PRIORITAS: Jika QRIS settlement via pooling
        if ($type === 'qris_pooling_settlement') {
            $pesananData['priority'] = true;
            $pesananData['settlement_time'] = $pesanan->pembayaran?->settlement_time;
            $pesananData['ready_for_confirmation'] = true;
            $pesananData['pooling_flow'] = 'settlement_detected';
            $pesananData['alert_message'] = 'QRIS SETTLEMENT BERHASIL! Customer sudah bayar dan klik Check Status. Siap dikonfirmasi!';
        }

        // Data spesifik berdasarkan jenis pesanan
        if ($jenisPesananNormalized === 'dinein') {
            $pesananData['nomor_meja'] = $pesanan->meja ? $pesanan->meja->nomor_meja : '01';
            $pesananData['tipe_meja'] = $pesanan->meja ? $pesanan->meja->tipe_meja : 'Regular';
            $pesananData['lantai'] = $pesanan->meja ? $pesanan->meja->lantai : '1';
        } elseif ($jenisPesananNormalized === 'takeaway') {
            $pesananData['nama_pelanggan'] = $pesanan->nama_pelanggan ?? 'Customer';
            $pesananData['nomor_wa'] = $pesanan->nomor_wa ?? '081234567890';
        }

        // Detail menu
        $menuItems = [];
        $totalItems = 0;
        
        if ($pesanan->detailPesanan && count($pesanan->detailPesanan) > 0) {
            foreach ($pesanan->detailPesanan as $detail) {
                $menuItems[] = [
                    'nama_menu' => $detail->menu->nama_menu ?? 'Menu tidak ditemukan',
                    'jumlah' => $detail->jumlah,
                    'harga' => $detail->menu->harga ?? 0,
                    'subtotal' => $detail->subtotal
                ];
                $totalItems += $detail->jumlah;
            }
        }
        
        $pesananData['menu_items'] = $menuItems;
        $pesananData['total_items'] = $totalItems;

        // ✅ Audio duration lebih lama untuk QRIS pooling settlement
        $audioDuration = $type === 'qris_pooling_settlement' ? 25000 : 16000;
        $notificationDuration = $type === 'qris_pooling_settlement' ? 12000 : 7000;

        Log::info("✅ New order notification - Midtrans Pooling: " . json_encode([
            'type' => $type,
            'pesanan_id' => $pesanan->id,
            'metode' => $pesanan->metode_pembayaran,
            'pooling_status' => $type === 'qris_pooling_settlement' ? 'priority_settlement' : 'regular'
        ]));

        return response()->json([
            'new_pesanan' => true,
            'pesanan_data' => $pesananData,
            'should_refresh' => false,
            'timestamp' => now()->timestamp,
            'notification_duration' => $notificationDuration,
            'audio_duration' => $audioDuration,
            'pooling_flow' => true,
            'success' => true
        ], 200);
    }

    public function saveCustomerData(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:20',
            'tanggal_pesanan' => 'required|date',
            'waktu_pesanan' => 'required',
        ]);

        session([
            'jenis_pesanan' => 'takeaway',
            'takeaway' => [
                'nama_pelanggan' => $request->nama_pelanggan,
                'nomor_wa' => $request->nomor_wa,
                'tanggal_pesanan' => $request->tanggal_pesanan,
                'waktu_pesanan' => $request->waktu_pesanan,
            ]
        ]);

        return redirect()->route('takeaway.booking')
            ->with('success', 'Data pelanggan berhasil disimpan. Silakan pilih menu.');
    }

    /**
     * ✅ QRIS SETTLEMENT MONITORING - Khusus untuk Midtrans Pooling
     */
    public function qrisSettlementStatus()
    {
        try {
            // ✅ Ambil pesanan QRIS yang settlement via pooling tapi belum dikonfirmasi kasir
            $pendingConfirmations = Pesanan::with(['pembayaran', 'meja'])
                ->where('metode_pembayaran', 'qris')
                ->where('status_pesanan', 'pending')
                ->whereHas('pembayaran', function($q) {
                    $q->where('status_pembayaran', 'dibayar');
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $pendingConfirmations->map(function($pesanan) {
                return [
                    'id' => $pesanan->id,
                    'order_id' => $pesanan->pembayaran->order_id,
                    'midtrans_order_id' => $pesanan->midtrans_order_id,
                    'total_harga' => $pesanan->total_harga,
                    'settlement_time' => $pesanan->pembayaran->settlement_time,
                    'jenis_pesanan' => $pesanan->jenis_pesanan,
                    'nomor_meja' => $pesanan->meja?->nomor_meja,
                    'nama_pelanggan' => $pesanan->nama_pelanggan,
                    'minutes_since_settlement' => $pesanan->pembayaran->settlement_time ? 
                        now()->diffInMinutes($pesanan->pembayaran->settlement_time) : null,
                    'pooling_status' => 'settled_waiting_confirmation'
                ];
            });

            return response()->json([
                'success' => true,
                'pending_confirmations' => $data,
                'count' => $data->count(),
                'timestamp' => now()->timestamp,
                'pooling_flow' => 'active',
                'message' => 'QRIS settlements via Midtrans Pooling'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error getting QRIS settlement status - Midtrans Pooling', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'pooling_flow' => 'error'
            ], 500);
        }
    }

    /**
     * ✅ POOLING DASHBOARD - Method baru untuk monitoring pooling status
     */
    public function poolingDashboard()
    {
        try {
            $stats = [
                // QRIS yang sudah settlement via pooling
                'qris_settled' => Pesanan::where('metode_pembayaran', 'qris')
                    ->whereHas('pembayaran', function($q) {
                        $q->where('status_pembayaran', 'dibayar');
                    })
                    ->where('status_pesanan', 'pending')
                    ->count(),
                
                // QRIS yang mungkin masih di cache (belum settlement)
                'qris_waiting' => Pesanan::where('metode_pembayaran', 'qris')
                    ->where('status_pesanan', 'pending')
                    ->whereDoesntHave('pembayaran', function($q) {
                        $q->where('status_pembayaran', 'dibayar');
                    })
                    ->count(),
                
                // Cash pending
                'cash_pending' => Pesanan::where('metode_pembayaran', 'cash')
                    ->where('status_pesanan', 'pending')
                    ->count(),
                
                // Total completed today
                'completed_today' => Pesanan::whereDate('created_at', today())
                    ->where('status_pesanan', 'selesai')
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'pooling_stats' => $stats,
                'timestamp' => now()->timestamp,
                'message' => 'Midtrans Pooling Dashboard Data'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}