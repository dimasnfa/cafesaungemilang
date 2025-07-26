<?php

namespace App\Http\Middleware;

use Closure;

class CheckDineinSession
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if (!session()->has('meja_id') || session('jenis_pesanan') !== 'dinein') {
            return redirect()->route('home')->with('error', 'Akses tidak sah. Silakan scan QR kembali.');
        }

        return $next($request);
    }
}
