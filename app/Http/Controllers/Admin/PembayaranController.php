<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    /**
     * ✅ INDEX - DISESUAIKAN dengan konsep MidtransPooling
     * Prioritaskan QRIS yang ready untuk konfirmasi kasir
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with(['pesanan.meja', 'pesanan.detailPesanan.menu'])
                          ->latest();

        // Filter berdasarkan jenis pesanan
        if ($request->has('jenis') && in_array($request->jenis, ['dinein', 'takeaway'])) {
            $query->where('jenis_pesanan', $request->jenis);
        }

        // Filter berdasarkan metode pembayaran
        if ($request->has('metode') && in_array($request->metode, ['qris', 'cash'])) {
            $query->where('metode_pembayaran', $request->metode);
        }

        // Filter berdasarkan status pembayaran
        if ($request->has('status') && in_array($request->status, ['pending', 'dibayar', 'gagal'])) {
            $query->where('status_pembayaran', $request->status);
        }

        // ✅ PERBAIKAN: Filter khusus QRIS ready untuk konfirmasi kasir (sesuai konsep MidtransPooling)
        if ($request->has('ready_confirmation') && $request->ready_confirmation == '1') {
            $query->where('metode_pembayaran', 'qris')
                  ->where('status_pembayaran', 'dibayar') // Sudah settlement dari MidtransPooling
                  ->whereHas('pesanan', function($q) {
                      $q->where('status_pesanan', 'pending'); // Pesanan masih pending, butuh konfirmasi kasir
                  });
        }

        $pembayarans = $query->paginate(10);

        // ✅ STATISTIK sesuai konsep MidtransPooling
        $statistics = [
            'total_pembayaran' => Pembayaran::count(),
            
            // 🎯 KUNCI: QRIS yang sudah settlement tapi pesanan masih pending (prioritas utama)
            'qris_ready_confirmation' => Pembayaran::where('metode_pembayaran', 'qris')
                                                    ->where('status_pembayaran', 'dibayar')
                                                    ->whereHas('pesanan', function($q) {
                                                        $q->where('status_pesanan', 'pending');
                                                    })->count(),
            
            'qris_completed_today' => Pembayaran::where('metode_pembayaran', 'qris')
                                                ->where('status_pembayaran', 'dibayar')
                                                ->whereHas('pesanan', function($q) {
                                                    $q->where('status_pesanan', 'dibayar');
                                                })
                                                ->whereDate('settlement_time', today())->count(),
            
            'cash_pending' => Pembayaran::where('metode_pembayaran', 'cash')
                                       ->where('status_pembayaran', 'pending')->count(),
            
            // Total settlement hari ini (dari MidtransPooling)
            'settlement_today' => Pembayaran::where('metode_pembayaran', 'qris')
                                           ->where('status_pembayaran', 'dibayar')
                                           ->whereDate('settlement_time', today())->count(),
        ];

        Log::info('📊 Pembayaran Index - MidtransPooling Ready', [
            'filters' => $request->only(['jenis', 'metode', 'status', 'ready_confirmation']),
            'qris_ready_confirmation' => $statistics['qris_ready_confirmation'],
            'total_records' => $pembayarans->total()
        ]);

        return view('admin.pages.pembayaran.index', compact('pembayarans', 'statistics'));
    }

    /**
     * ✅ STORE - Disesuaikan dengan konsep MidtransPooling
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id'          => 'required|unique:pembayaran,order_id',
            'total_bayar'       => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:qris,cash',
            'jenis_pesanan'     => 'required|in:dinein,takeaway',
            'nama_pelanggan'    => 'nullable|string|max:100',
            'nomor_wa'          => 'nullable|string|max:20',
            'pesanan_id'        => 'required|exists:pesanan,id',
        ]);

        $pesanan = Pesanan::with('meja')->findOrFail($request->pesanan_id);

        // ✅ PERBAIKAN: Status pembayaran sesuai alur MidtransPooling
        $statusPembayaran = 'pending'; // Default untuk manual entry
        
        // CATATAN: Untuk QRIS otomatis, status "dibayar" hanya dari MidtransPooling settlement
        // Manual admin entry tetap "pending" sampai ada settlement/konfirmasi

        $data = [
            'pesanan_id'        => $request->pesanan_id,
            'order_id'          => $request->order_id,
            'total_bayar'       => $request->total_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status_pembayaran' => $statusPembayaran,
            'jenis_pesanan'     => $request->jenis_pesanan,
            'nama_pelanggan'    => $request->nama_pelanggan,
            'nomor_wa'          => $request->nomor_wa,
            'nomor_meja'        => $pesanan->meja?->nomor_meja,
            'tanggal_pesanan'   => $pesanan->tanggal_pesanan,
            'waktu_pesanan'     => $pesanan->waktu_pesanan,
        ];

        $pembayaran = Pembayaran::create($data);

        Log::info('💰 Pembayaran manual created - awaiting MidtransPooling flow', [
            'order_id' => $pembayaran->order_id,
            'metode' => $pembayaran->metode_pembayaran,
            'status' => $pembayaran->status_pembayaran,
            'note' => 'Will be processed via MidtransPooling for QRIS or cash confirmation'
        ]);

        return redirect()->route('admin.pembayaran.index')
                        ->with('success', 'Pembayaran berhasil ditambahkan. Status akan diupdate melalui sistem MidtransPooling.');
    }

    public function create()
    {
        $pesanans = Pesanan::with('meja')
                           ->whereDoesntHave('pembayaran')
                           ->latest()
                           ->get();
        
        return view('admin.pages.pembayaran.create', compact('pesanans'));
    }

    public function edit($id)
    {
        $pembayaran = Pembayaran::with('pesanan.meja')->findOrFail($id);
        $pesanans = Pesanan::with('meja')->get();
        
        return view('admin.pages.pembayaran.edit', compact('pembayaran', 'pesanans'));
    }

    /**
     * ✅ UPDATE - PERBAIKAN UTAMA: Validasi sesuai alur MidtransPooling
     */
    public function update(Request $request, $id)
    {
        $pembayaran = Pembayaran::with('pesanan')->findOrFail($id);
        
        $request->validate([
            'order_id'          => 'required|unique:pembayaran,order_id,' . $pembayaran->id,
            'total_bayar'       => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:qris,cash',
            'status_pembayaran' => 'required|in:pending,dibayar,gagal',
            'jenis_pesanan'     => 'required|in:dinein,takeaway',
            'nama_pelanggan'    => 'nullable|string|max:100',
            'nomor_wa'          => 'nullable|string|max:20',
            'pesanan_id'        => 'required|exists:pesanan,id',
        ]);

        $pesanan = Pesanan::with('meja')->findOrFail($request->pesanan_id);
        $oldStatus = $pembayaran->status_pembayaran;
        
        // ✅ VALIDASI KHUSUS: Protect MidtransPooling flow
        if ($request->metode_pembayaran === 'qris' && 
            $request->status_pembayaran === 'dibayar' && 
            $oldStatus !== 'dibayar' &&
            empty($pembayaran->settlement_time)) {
            
            Log::warning('🚫 Attempt to manually set QRIS to dibayar without MidtransPooling settlement', [
                'order_id' => $pembayaran->order_id,
                'admin_action' => true,
                'current_settlement_time' => $pembayaran->settlement_time
            ]);
            
            return redirect()->back()
                            ->with('error', '🚫 Status pembayaran QRIS "dibayar" hanya bisa diupdate melalui MidtransPooling settlement, bukan manual admin!');
        }
        
        $updateData = [
            'pesanan_id'        => $request->pesanan_id,
            'order_id'          => $request->order_id,
            'total_bayar'       => $request->total_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status_pembayaran' => $request->status_pembayaran,
            'jenis_pesanan'     => $request->jenis_pesanan,
            'nama_pelanggan'    => $request->nama_pelanggan,
            'nomor_wa'          => $request->nomor_wa,
            'nomor_meja'        => $pesanan->meja?->nomor_meja,
        ];

        // ✅ Set settlement_time untuk cash yang dikonfirmasi manual
        if ($request->status_pembayaran === 'dibayar' && 
            $request->metode_pembayaran === 'cash' &&
            !$pembayaran->settlement_time) {
            $updateData['settlement_time'] = now();
        }

        $pembayaran->update($updateData);

        Log::info('✅ Pembayaran updated - MidtransPooling compliant', [
            'order_id' => $pembayaran->order_id,
            'old_status' => $oldStatus,
            'new_status' => $request->status_pembayaran,
            'metode' => $request->metode_pembayaran,
            'settlement_time' => $pembayaran->settlement_time
        ]);

        return redirect()->route('admin.pembayaran.index')
                        ->with('success', 'Data pembayaran berhasil diperbarui sesuai alur MidtransPooling.');
    }

    /**
     * ✅ SHOW - Enhanced dengan info MidtransPooling flow
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with([
            'pesanan.meja', 
            'pesanan.detailPesanan.menu'
        ])->findOrFail($id);
        
        // ✅ INFO ENHANCED sesuai konsep MidtransPooling
        $flowInfo = [
            'is_qris_settled' => $pembayaran->isQrisSettled(),
            'ready_for_cashier_confirmation' => $pembayaran->isReadyForCashierConfirmation(),
            'payment_flow_status' => $this->getPaymentFlowStatus($pembayaran),
            'next_action_required' => $this->getNextActionRequired($pembayaran),
            'settlement_source' => $this->getSettlementSource($pembayaran),
            'pooling_compatible' => $this->isPoolingCompatible($pembayaran),
        ];
        
        return view('admin.pages.pembayaran.show', compact('pembayaran', 'flowInfo'));
    }

    /**
     * ✅ ENHANCED: Payment flow status sesuai MidtransPooling
     */
    private function getPaymentFlowStatus($pembayaran)
    {
        if ($pembayaran->metode_pembayaran === 'qris') {
            if ($pembayaran->status_pembayaran === 'pending') {
                return '⏳ Waiting for MidtransPooling Settlement';
            } elseif ($pembayaran->status_pembayaran === 'dibayar' && 
                     $pembayaran->pesanan && 
                     $pembayaran->pesanan->status_pesanan === 'pending') {
                return '✅ QRIS Settled via Pooling - Ready for Cashier Confirmation';
            } elseif ($pembayaran->status_pembayaran === 'dibayar' && 
                     $pembayaran->pesanan && 
                     $pembayaran->pesanan->status_pesanan === 'dibayar') {
                return '🎉 QRIS Complete - Order Processing';
            }
        } elseif ($pembayaran->metode_pembayaran === 'cash') {
            if ($pembayaran->status_pembayaran === 'pending') {
                return '💵 Cash - Awaiting Cashier Confirmation';
            } elseif ($pembayaran->status_pembayaran === 'dibayar') {
                return '✅ Cash Confirmed - Processing Order';
            }
        }

        return '❓ Status: ' . $pembayaran->status_pembayaran;
    }

    /**
     * ✅ ENHANCED: Next action sesuai MidtransPooling flow
     */
    private function getNextActionRequired($pembayaran)
    {
        if ($pembayaran->metode_pembayaran === 'qris') {
            if ($pembayaran->status_pembayaran === 'pending') {
                return '🔄 Wait for customer QRIS payment → MidtransPooling will auto-settle';
            } elseif ($pembayaran->isReadyForCashierConfirmation()) {
                return '👨‍💼 Cashier: Confirm order in Pesanan menu (payment already settled)';
            } elseif ($pembayaran->status_pembayaran === 'dibayar' && 
                     $pembayaran->pesanan?->status_pesanan === 'dibayar') {
                return '👨‍🍳 Kitchen: Process order';
            }
        } elseif ($pembayaran->metode_pembayaran === 'cash') {
            if ($pembayaran->status_pembayaran === 'pending') {
                return '👨‍💼 Cashier: Confirm cash payment received';
            } elseif ($pembayaran->status_pembayaran === 'dibayar' &&
                     $pembayaran->pesanan?->status_pesanan === 'pending') {
                return '👨‍💼 Cashier: Confirm order in Pesanan menu';
            }
        }

        return '✅ No action required';
    }

    /**
     * ✅ NEW: Get settlement source info
     */
    private function getSettlementSource($pembayaran)
    {
        if ($pembayaran->metode_pembayaran === 'qris' && 
            $pembayaran->status_pembayaran === 'dibayar') {
            
            if ($pembayaran->settlement_time) {
                return '🤖 MidtransPooling Auto-Settlement';
            } else {
                return '👨‍💼 Manual Admin Settlement';
            }
        } elseif ($pembayaran->metode_pembayaran === 'cash' &&
                  $pembayaran->status_pembayaran === 'dibayar') {
            return '👨‍💼 Manual Cash Confirmation';
        }

        return '⏳ Not Settled';
    }

    /**
     * ✅ NEW: Check if payment is MidtransPooling compatible
     */
    private function isPoolingCompatible($pembayaran)
    {
        // QRIS dengan settlement_time = compatible
        if ($pembayaran->metode_pembayaran === 'qris' && 
            $pembayaran->settlement_time) {
            return true;
        }

        // Cash selalu compatible
        if ($pembayaran->metode_pembayaran === 'cash') {
            return true;
        }

        return false;
    }

    /**
     * ✅ DESTROY - Enhanced dengan MidtransPooling protection
     */
    public function destroy($id)
    {
        $pembayaran = Pembayaran::with('pesanan')->findOrFail($id);
        
        // ✅ PROTECTION: Tidak bisa hapus settlement dari MidtransPooling
        if ($pembayaran->metode_pembayaran === 'qris' && 
            $pembayaran->status_pembayaran === 'dibayar' &&
            $pembayaran->settlement_time) {
            
            return redirect()->route('admin.pembayaran.index')
                            ->with('error', '🚫 Tidak dapat menghapus pembayaran QRIS yang sudah settlement via MidtransPooling!');
        }

        // ✅ PROTECTION: Tidak bisa hapus jika pesanan sudah diproses
        if ($pembayaran->pesanan && $pembayaran->pesanan->status_pesanan === 'dibayar') {
            return redirect()->route('admin.pembayaran.index')
                            ->with('error', '🚫 Tidak dapat menghapus pembayaran dengan pesanan yang sudah dikonfirmasi!');
        }

        Log::info('🗑️ Pembayaran deleted - MidtransPooling safe', [
            'order_id' => $pembayaran->order_id,
            'status' => $pembayaran->status_pembayaran,
            'metode' => $pembayaran->metode_pembayaran,
            'had_settlement' => !is_null($pembayaran->settlement_time)
        ]);

        $pembayaran->delete();

        return redirect()->route('admin.pembayaran.index')
                        ->with('success', 'Data pembayaran berhasil dihapus.');
    }

    /**
     * ✅ ENHANCED: MidtransPooling Status Monitor
     */
    public function checkStatus(Request $request)
    {
        $query = Pembayaran::with('pesanan');

        if ($request->has('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // ✅ Focus on recent payments (1 hour) untuk real-time monitoring
        $recentPayments = $query->where('created_at', '>=', now()->subHour())
                               ->latest()
                               ->get()
                               ->map(function ($payment) {
                                   return [
                                       'id' => $payment->id,
                                       'order_id' => $payment->order_id,
                                       'metode_pembayaran' => $payment->metode_pembayaran,
                                       'status_pembayaran' => $payment->status_pembayaran,
                                       'status_pesanan' => $payment->pesanan?->status_pesanan,
                                       'total_bayar' => $payment->total_bayar,
                                       'settlement_time' => $payment->settlement_time,
                                       'settlement_source' => $this->getSettlementSource($payment),
                                       'flow_status' => $this->getPaymentFlowStatus($payment),
                                       'next_action' => $this->getNextActionRequired($payment),
                                       'pooling_ready' => $payment->isReadyForCashierConfirmation(),
                                       'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                                   ];
                               });

        return response()->json([
            'success' => true,
            'data' => $recentPayments,
            'midtrans_pooling_active' => true,
            'timestamp' => now()->timestamp
        ]);
    }

    /**
     * ✅ ENHANCED: MidtransPooling Dashboard
     */
    public function midtransPoolingDashboard()
    {
        try {
            // ✅ QRIS settlements waiting for cashier confirmation (prioritas tinggi)
            $qrisWaitingConfirmation = Pembayaran::with(['pesanan.meja', 'pesanan.detailPesanan'])
                ->where('metode_pembayaran', 'qris')
                ->where('status_pembayaran', 'dibayar')
                ->whereHas('pesanan', function($q) {
                    $q->where('status_pesanan', 'pending');
                })
                ->orderBy('settlement_time', 'asc')
                ->get();

            // ✅ QRIS settlements completed today
            $qrisCompletedToday = Pembayaran::where('metode_pembayaran', 'qris')
                ->where('status_pembayaran', 'dibayar')
                ->whereHas('pesanan', function($q) {
                    $q->where('status_pesanan', 'dibayar');
                })
                ->whereDate('settlement_time', today())
                ->count();

            // ✅ QRIS pending (waiting settlement)
            $qrisPending = Pembayaran::where('metode_pembayaran', 'qris')
                ->where('status_pembayaran', 'pending')
                ->where('created_at', '>=', now()->subHours(2))
                ->get();

            // ✅ Performance metrics
            $metrics = [
                'qris_waiting_confirmation' => $qrisWaitingConfirmation->count(),
                'qris_completed_today' => $qrisCompletedToday,
                'qris_pending_settlement' => $qrisPending->count(),
                'total_revenue_today' => Pembayaran::where('status_pembayaran', 'dibayar')
                                                   ->whereDate('settlement_time', today())
                                                   ->sum('total_bayar'),
                'average_settlement_time' => $this->getAverageSettlementTime(),
            ];

            return view('admin.pages.pembayaran.midtrans-pooling-dashboard', compact(
                'qrisWaitingConfirmation', 
                'qrisPending', 
                'metrics'
            ));

        } catch (\Exception $e) {
            Log::error('❌ Error in MidtransPooling dashboard', [
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error loading MidtransPooling dashboard: ' . $e->getMessage());
        }
    }

    /**
     * ✅ NEW: Get average settlement time
     */
    private function getAverageSettlementTime()
    {
        $settlements = Pembayaran::where('metode_pembayaran', 'qris')
            ->where('status_pembayaran', 'dibayar')
            ->whereNotNull('settlement_time')
            ->whereDate('created_at', today())
            ->get();

        if ($settlements->isEmpty()) {
            return 0;
        }

        $totalMinutes = $settlements->sum(function($payment) {
            return $payment->created_at->diffInMinutes($payment->settlement_time);
        });

        return round($totalMinutes / $settlements->count(), 2);
    }

    /**
     * ✅ SYNC STATUS - Enhanced untuk MidtransPooling compatibility
     */
    public function syncStatus($id)
    {
        $pembayaran = Pembayaran::with('pesanan')->findOrFail($id);
        
        Log::info('🔄 Manual sync pembayaran - MidtransPooling check', [
            'order_id' => $pembayaran->order_id,
            'current_payment_status' => $pembayaran->status_pembayaran,
            'current_order_status' => $pembayaran->pesanan?->status_pesanan,
            'settlement_time' => $pembayaran->settlement_time
        ]);

        // ✅ QRIS: Tidak bisa sync manual jika belum ada settlement dari MidtransPooling
        if ($pembayaran->metode_pembayaran === 'qris' && 
            !$pembayaran->settlement_time) {
            
            return redirect()->back()
                            ->with('info', '🔄 QRIS payment harus menunggu settlement via MidtransPooling terlebih dahulu.');
        }

        // ✅ CASH: Sinkronisasi status pembayaran mengikuti status pesanan
        if ($pembayaran->metode_pembayaran === 'cash' && 
            $pembayaran->pesanan && 
            $pembayaran->pesanan->status_pesanan === 'dibayar' && 
            $pembayaran->status_pembayaran === 'pending') {
            
            $pembayaran->update([
                'status_pembayaran' => 'dibayar',
                'settlement_time' => now()
            ]);
            
            return redirect()->back()
                            ->with('success', '✅ Status pembayaran cash berhasil disinkronisasi.');
        }

        return redirect()->back()
                        ->with('info', '✅ Status sudah sinkron atau tidak perlu perubahan.');
    }

    /**
     * ✅ INVOICE - Enhanced dengan MidtransPooling info
     */
    public function invoice($id)
    {
        $pembayaran = Pembayaran::with([
            'pesanan.meja',
            'pesanan.detailPesanan.menu'
        ])->findOrFail($id);

        $invoiceInfo = [
            'settlement_time' => $pembayaran->settlement_time,
            'settlement_source' => $this->getSettlementSource($pembayaran),
            'pooling_processed' => $this->isPoolingCompatible($pembayaran),
            'flow_completed' => $pembayaran->status_pembayaran === 'dibayar' && 
                               $pembayaran->pesanan?->status_pesanan === 'dibayar',
        ];
        
        return view('admin.pages.pembayaran.invoice', compact('pembayaran', 'invoiceInfo'));
    }

    /**
     * ✅ NEW: MidtransPooling Health Check
     */
    public function healthCheck()
    {
        try {
            $health = [
                'status' => 'healthy',
                'qris_pending_count' => Pembayaran::where('metode_pembayaran', 'qris')
                                                  ->where('status_pembayaran', 'pending')
                                                  ->where('created_at', '>=', now()->subHour())
                                                  ->count(),
                'qris_ready_confirmation' => Pembayaran::where('metode_pembayaran', 'qris')
                                                       ->where('status_pembayaran', 'dibayar')
                                                       ->whereHas('pesanan', function($q) {
                                                           $q->where('status_pesanan', 'pending');
                                                       })->count(),
                'last_settlement' => Pembayaran::where('metode_pembayaran', 'qris')
                                               ->where('status_pembayaran', 'dibayar')
                                               ->latest('settlement_time')
                                               ->value('settlement_time'),
                'pooling_active' => true,
                'timestamp' => now()
            ];

            return response()->json($health);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'pooling_active' => false
            ], 500);
        }
    }
}