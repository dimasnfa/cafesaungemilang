<?php

namespace App\Providers;

use App\Models\Cart;
use App\Services\HttpsUrlService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('https_url', function($app) {
            return new HttpsUrlService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $cartCount = 0;
            $cartCountDinein = 0;
            $cartCountTakeaway = 0;

            // Hitung keranjang untuk admin/kasir (semua cart total)
            if (Auth::check() && Auth::user()->hasRole(['admin', 'kasir'])) {
                $cartCount = Cart::sum('qty');
            }

            // Hitung keranjang Dine-In berdasarkan meja_id session
            $mejaId = session('meja_id');
            if ($mejaId) {
                $cartCountDinein = Cart::where('meja_id', $mejaId)->sum('qty');
            }

            // Hitung keranjang Takeaway berdasarkan nomor_wa di session takeaway
            if (Session::get('jenis_pesanan') == 'takeaway' && Session::has('takeaway.nomor_wa')) {
                $nomorWa = Session::get('takeaway.nomor_wa');
                $cartCountTakeaway = Cart::where('nomor_wa', $nomorWa)
                    ->where('jenis_pesanan', 'takeaway')
                    ->sum('qty');
            }

            // Set ke session agar bisa dipakai juga jika butuh
            session([
                'cart_count' => $cartCount,
                'cart_count_dinein' => $cartCountDinein,
                'cart_count_takeaway' => $cartCountTakeaway,
            ]);

            // Kirim ke semua view
            $view->with([
                'cartCount' => $cartCount,
                'cartCountDinein' => $cartCountDinein,
                'cartCountTakeaway' => $cartCountTakeaway,
            ]);
            
        });
    }
}
