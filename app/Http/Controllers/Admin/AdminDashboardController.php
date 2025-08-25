<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Meja;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Data statistik umum
        $totalPesanan = Pesanan::count();
        $totalMenu = Menu::count();
        $totalMeja = Meja::count();
        $totalKategori = Kategori::count();

        // Pesanan hari ini
        $pesananHariIni = Pesanan::whereDate('tanggal_pesanan', today())->count();

        // Total pendapatan
        $totalPendapatan = Pesanan::where('status_pesanan', 'dibayar')->sum('total_harga');

        // FIXED: Data untuk diagram bulat - Jenis Pesanan
        $jenisPesananDataRaw = Pesanan::select('jenis_pesanan', DB::raw('count(*) as total'))
            ->groupBy('jenis_pesanan')
            ->pluck('total', 'jenis_pesanan');

        // Urutan dipastikan: dinein dulu, lalu takeaway
        $jenisPesananData = collect([
            ['jenis_pesanan' => 'dinein', 'total' => $jenisPesananDataRaw['dinein'] ?? 0],
            ['jenis_pesanan' => 'takeaway', 'total' => $jenisPesananDataRaw['takeaway'] ?? 0],
        ]);

        // Data untuk diagram batang - Menu Populer (Top 10)
        $menuPopuler = DetailPesanan::select('menu_id', DB::raw('SUM(jumlah) as total_terjual'))
            ->with('menu')
            ->groupBy('menu_id')
            ->orderBy('total_terjual', 'desc')
            ->limit(10)
            ->get();

        // Data pesanan berdasarkan status
        $statusPesanan = Pesanan::select('status_pesanan', DB::raw('count(*) as total'))
            ->groupBy('status_pesanan')
            ->get();

        // Pesanan terbaru (5 terakhir)
        $pesananTerbaru = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Data untuk chart pendapatan 7 hari terakhir
        $pendapatanMingguan = Pesanan::select(
                DB::raw('DATE(tanggal_pesanan) as tanggal'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('status_pesanan', 'dibayar')
            ->where('tanggal_pesanan', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        return view('admin.pages.dashboard.index', compact(
            'totalPesanan',
            'totalMenu', 
            'totalMeja',
            'totalKategori',
            'pesananHariIni',
            'totalPendapatan',
            'jenisPesananData',
            'menuPopuler',
            'statusPesanan',
            'pesananTerbaru',
            'pendapatanMingguan'
        ));
    }

    // Optional: Ajax chart loader
    public function getChartData(Request $request)
    {
        $type = $request->get('type');

        switch ($type) {
            case 'jenis_pesanan':
                $jenisPesananDataRaw = Pesanan::select('jenis_pesanan', DB::raw('count(*) as total'))
                    ->groupBy('jenis_pesanan')
                    ->pluck('total', 'jenis_pesanan');

                $data = collect([
                    ['jenis_pesanan' => 'dinein', 'total' => $jenisPesananDataRaw['dinein'] ?? 0],
                    ['jenis_pesanan' => 'takeaway', 'total' => $jenisPesananDataRaw['takeaway'] ?? 0],
                ]);
                break;

            case 'menu_populer':
                $data = DetailPesanan::select('menu_id', DB::raw('SUM(jumlah) as total_terjual'))
                    ->with('menu')
                    ->groupBy('menu_id')
                    ->orderBy('total_terjual', 'desc')
                    ->limit(10)
                    ->get();
                break;

            case 'pendapatan_bulanan':
                $data = Pesanan::select(
                        DB::raw('MONTH(tanggal_pesanan) as bulan'),
                        DB::raw('YEAR(tanggal_pesanan) as tahun'),
                        DB::raw('SUM(total_harga) as total')
                    )
                    ->where('status_pesanan', 'dibayar')
                    ->whereYear('tanggal_pesanan', date('Y'))
                    ->groupBy('bulan', 'tahun')
                    ->orderBy('bulan')
                    ->get();
                break;

            default:
                $data = [];
        }

        return response()->json($data);
    }
}
