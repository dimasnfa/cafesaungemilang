<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckTakeawaySession
{
    public function handle(Request $request, Closure $next)
    {
        $takeaway = Session::get('takeaway');
        $jenisPesanan = Session::get('jenis_pesanan');

        if (!$takeaway || $jenisPesanan !== 'takeaway' || 
            !isset(
                $takeaway['nama_pelanggan'], 
                $takeaway['nomor_wa'], 
                $takeaway['tanggal_pesanan'], 
                $takeaway['waktu_pesanan']
            )
        ) {
            // Jika data pelanggan takeaway belum ada, redirect ke form data pelanggan
            return redirect()->route('takeaway.customer.form')->with('error', 'Silakan isi data pelanggan terlebih dahulu.');
        }

        return $next($request);
    }
}
