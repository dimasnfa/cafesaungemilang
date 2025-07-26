<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekomendasiMenuController extends Controller
{
    /**
     * Untuk admin - tampilkan 5 menu paling sering dipesan (semua jenis pesanan)
     */
    public function index()
    {
        $topMenuIds = DB::table('detailpesanan')
            ->join('pesanan', 'detailpesanan.pesanan_id', '=', 'pesanan.id')
            ->select('detailpesanan.menu_id', DB::raw('SUM(detailpesanan.jumlah) as total'))
            ->groupBy('detailpesanan.menu_id')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('detailpesanan.menu_id')
            ->toArray();

        $menus = Menu::withSum('detailpesanan', 'jumlah')
            ->whereIn('id', $topMenuIds)
            ->orderByRaw("FIELD(id, " . implode(',', $topMenuIds) . ")")
            ->get();

        $kategoris = Kategori::all();

        return view('admin.rekomendasi.index', compact('menus', 'kategoris'));
    }

    /**
     * Untuk Dine-in: tampilkan rekomendasi menu jika sudah ada session meja_id
     */
    public function showRekomendasi(Request $request)
    {
        if (!session()->has('meja_id')) {
            return redirect()->route('booking', ['jenis' => 'dinein']);
        }

        session(['jenis_pesanan' => 'dinein']);
        $meja_id = session('meja_id');

        $topMenuIds = DB::table('detailpesanan')
            ->join('pesanan', 'detailpesanan.pesanan_id', '=', 'pesanan.id')
            ->where('pesanan.jenis_pesanan', 'dinein')
            ->select('detailpesanan.menu_id', DB::raw('SUM(detailpesanan.jumlah) as total'))
            ->groupBy('detailpesanan.menu_id')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('detailpesanan.menu_id')
            ->toArray();

        $menus = Menu::with('kategori')
            ->withSum(['detailpesanan as detailpesanan_sum_jumlah' => function ($q) {
                $q->whereHas('pesanan', function ($q2) {
                    $q2->where('jenis_pesanan', 'dinein');
                });
            }], 'jumlah')
            ->whereIn('id', $topMenuIds)
            ->orderByRaw("FIELD(id, " . implode(',', $topMenuIds) . ")")
            ->get();

        return view('cart.dinein.rekomendasimenu', compact('menus', 'meja_id'));
    }

    /**
     * Untuk publik: tampilkan rekomendasi menu berdasarkan jenis pesanan
     */
    public function indexPublic($tipe)
    {
        if (!in_array($tipe, ['dinein', 'takeaway'])) {
            abort(404);
        }

        if ($tipe === 'dinein') {
            return redirect()->route('rekomendasi.dinein');
        }

        $topMenuIds = DB::table('detailpesanan')
            ->join('pesanan', 'detailpesanan.pesanan_id', '=', 'pesanan.id')
            ->where('pesanan.jenis_pesanan', $tipe)
            ->select('detailpesanan.menu_id', DB::raw('SUM(detailpesanan.jumlah) as total'))
            ->groupBy('detailpesanan.menu_id')
            ->orderByDesc('total')
            ->pluck('detailpesanan.menu_id')
            ->toArray();

        $menus = Menu::with('kategori')
            ->withSum(['detailpesanan as detailpesanan_sum_jumlah' => function ($q) use ($tipe) {
                $q->whereHas('pesanan', function ($q2) use ($tipe) {
                    $q2->where('jenis_pesanan', $tipe);
                });
            }], 'jumlah')
            ->whereIn('id', $topMenuIds)
            ->orderByRaw("FIELD(id, " . implode(',', $topMenuIds) . ")")
            ->get();

        $kategoris = Kategori::all();

        return view('cart.takeaway.rekomendasimenu', compact('menus', 'kategoris'));
    }
}
