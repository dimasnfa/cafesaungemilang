<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailPesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->input('filter_type', 'harian');
        $tanggal = $request->input('harian');
        $bulan = $request->input('bulanan');
        $tahun = $request->input('tahunan');

        $query = DetailPesanan::with(['menu.kategori', 'pesanan'])
            ->whereHas('pesanan', function ($q) use ($filterType, $tanggal, $bulan, $tahun) {
                if ($filterType === 'harian' && $tanggal) {
                    $q->whereDate('tanggal_pesanan', $tanggal);
                } elseif ($filterType === 'bulanan' && $bulan) {
                    $q->whereYear('tanggal_pesanan', substr($bulan, 0, 4))
                      ->whereMonth('tanggal_pesanan', substr($bulan, 5, 2));
                } elseif ($filterType === 'tahunan' && $tahun) {
                    $q->whereYear('tanggal_pesanan', $tahun);
                }
            });

        $laporans = $query->get();
        $totalPendapatan = $laporans->pluck('pesanan.total_harga')->unique()->sum();

        return view('admin.pages.laporan.index', compact(
            'laporans', 'filterType', 'tanggal', 'bulan', 'tahun', 'totalPendapatan'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filterType = $request->input('filter_type', 'harian');
        $tanggal = $request->input('harian');
        $bulan = $request->input('bulanan');
        $tahun = $request->input('tahunan');

        $query = DetailPesanan::with(['menu.kategori', 'pesanan'])
            ->whereHas('pesanan', function ($q) use ($filterType, $tanggal, $bulan, $tahun) {
                if ($filterType === 'harian' && $tanggal) {
                    $q->whereDate('tanggal_pesanan', $tanggal);
                } elseif ($filterType === 'bulanan' && $bulan) {
                    $q->whereYear('tanggal_pesanan', substr($bulan, 0, 4))
                      ->whereMonth('tanggal_pesanan', substr($bulan, 5, 2));
                } elseif ($filterType === 'tahunan' && $tahun) {
                    $q->whereYear('tanggal_pesanan', $tahun);
                }
            });

        $laporans = $query->get();
        $totalPendapatan = $laporans->pluck('pesanan.total_harga')->unique()->sum();

        $pdf = Pdf::loadView('admin.pages.laporan.cetakpdf', [
            'laporans' => $laporans,
            'filterType' => $filterType,
            'tanggal' => $tanggal,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'tanggalCetak' => Carbon::now()->translatedFormat('d-m-Y'),
            'totalPendapatan' => $totalPendapatan
        ]);

        return $pdf->download('laporan-pesanan.pdf');
    }

    public function exportCsv(Request $request)
    {
        $filterType = $request->input('filter_type', 'harian');
        $tanggal = $request->input('harian');
        $bulan = $request->input('bulanan');
        $tahun = $request->input('tahunan');

        $query = DetailPesanan::with(['menu.kategori', 'pesanan'])
            ->whereHas('pesanan', function ($q) use ($filterType, $tanggal, $bulan, $tahun) {
                if ($filterType === 'harian' && $tanggal) {
                    $q->whereDate('tanggal_pesanan', $tanggal);
                } elseif ($filterType === 'bulanan' && $bulan) {
                    $q->whereYear('tanggal_pesanan', substr($bulan, 0, 4))
                      ->whereMonth('tanggal_pesanan', substr($bulan, 5, 2));
                } elseif ($filterType === 'tahunan' && $tahun) {
                    $q->whereYear('tanggal_pesanan', $tahun);
                }
            });

        $laporans = $query->get();

        $response = new StreamedResponse(function () use ($laporans) {
            $handle = fopen('php://output', 'w');

            // Header kolom CSV
            fputcsv($handle, [
                'Tanggal',
                'Waktu',
                'Jenis Pesanan',
                'Menu',
                'Kategori',
                'Jumlah',
                'Subtotal',
                'Total Harga',
                'Nomor Meja',
                'Tipe Meja',
                'Lantai',
                'Nama Pelanggan',
                'Nomor WhatsApp'
            ]);

            foreach ($laporans as $laporan) {
                $pesanan = $laporan->pesanan;
                $meja = $pesanan->meja ?? null;

                fputcsv($handle, [
                    $pesanan->tanggal_pesanan,
                    $pesanan->waktu_pesanan,
                    ucfirst($pesanan->jenis_pesanan),
                    $laporan->menu->nama_menu,
                    $laporan->menu->kategori->nama_kategori ?? '-',
                    $laporan->jumlah,
                    $laporan->subtotal,
                    $pesanan->total_harga,
                    $meja->nomor_meja ?? '-',
                    $meja->tipe_meja ?? '-',
                    $meja->lantai ?? '-',
                    $pesanan->nama_pelanggan ?? '-',
                    $pesanan->nomor_wa ?? '-',
                ]);
            }

            fclose($handle);
        });

        $filename = 'laporan-pesanan-' . now()->format('Ymd_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }
}
