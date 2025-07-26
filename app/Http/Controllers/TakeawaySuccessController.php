<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;

class TakeawaySuccessController extends Controller
{
    public function index()
    {
        $nomorWa = session('takeaway.nomor_wa');

        if (!$nomorWa) {
            return redirect()->route('home')->with('error', 'Session nomor WhatsApp tidak ditemukan.');
        }

        $pesanan = Pesanan::where('nomor_wa', $nomorWa)
                    ->where('jenis_pesanan', 'takeaway')
                    ->latest()
                    ->first();

        if (!$pesanan) {
            return redirect()->route('home')->with('error', 'Data pesanan tidak ditemukan.');
        }

        return view('cart.takeaway.success', [
            'nama_pelanggan'     => $pesanan->nama_pelanggan,
            'nomor_wa'           => $pesanan->nomor_wa,
            'waktu_pesanan'      => $pesanan->waktu_pesanan ?? $pesanan->created_at,
            'metode_pembayaran'  => 'QRIS', // Hardcoded sesuai kebijakan takeaway
        ]);
    }
}
