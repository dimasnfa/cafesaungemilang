<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index');
    }

    public function about()
    {
        return view('landing.components.about');
    }

    public function service()
    {
        return view('landing.components.service');
    }

    public function menu()
    {
        $kategoris = Kategori::with('menus')->get();
        $menus = Menu::all();
        return view('landing.menu', compact('kategoris', 'menus'));
    }

    public function contact()
    {
        return view('landing.contact');
    }

    public function takeaway()
    {
        $kategoris = Kategori::with('menus')->get();
        $menus = Menu::all();
        return view('takeaway.booking', compact('kategoris', 'menus'));
    }

    public function dinein()
    {
        $kategoris = Kategori::with('menus')->get();
        $menus = Menu::all();
        return view('dinein.booking', compact('kategoris', 'menus'));
    }

    /**
     * Method utama untuk handle QR Code scan
     * URL: /dinein/booking/{meja_id}
     */
    public function booking($meja_id, Request $request)
    {
        // Validasi meja_id ada
        $meja = Meja::find($meja_id);
        if (!$meja) {
            return redirect()->route('home')->with('error', 'Meja tidak ditemukan.');
        }

        // Cek status meja
        if ($meja->status === 'terisi') {
            return redirect()->route('home')->with('error', 'Meja sedang terisi, silakan pilih meja lain.');
        }

        // Clear session lama untuk memastikan fresh start
        Session::forget(['meja_id', 'jenis_pesanan', 'meja_data', 'notif_meja']);

        // Set session baru untuk QR scan
        Session::put('meja_id', $meja_id);
        Session::put('jenis_pesanan', 'dinein');
        Session::put('notif_meja', true); // untuk popup notifikasi
        Session::put('meja_data', [
            'id' => $meja->id,
            'nomor_meja' => $meja->nomor_meja,
            'tipe_meja' => $meja->tipe_meja,
            'lantai' => $meja->lantai,
            'status' => $meja->status
        ]);

        // ✅ Set initial cart count untuk dine-in
        $cartCount = Cart::where('meja_id', $meja_id)
                        ->where('jenis_pesanan', 'dinein')
                        ->sum('qty');
        Session::put('cart_count_dinein', $cartCount);

        // Ambil data menu untuk booking page
        $kategoris = Kategori::with('menus')->get();
        $menus = Menu::all();

        // Langsung render halaman booking menu tanpa redirect
        return view('cart.dinein.booking', [
            'mejaData' => $meja,
            'showNotifikasi' => true, // selalu true untuk QR scan
            'kategoris' => $kategoris,
            'menus' => $menus,
        ]);
    }

    /**
     * Method alternatif jika diperlukan untuk handle scan QR dari form
     */
    public function scanQr(Request $request)
    {
        $mejaId = $request->input('meja_id');

        if (!$mejaId) {
            return redirect('/')->with('error', 'Meja tidak ditemukan.');
        }

        // Redirect langsung ke booking method tanpa parameter tambahan
        return redirect()->route('cart.dinein.booking.by.meja', ['meja_id' => $mejaId]);
    }

    /**
     * Method untuk handle akses booking tanpa QR (misalnya dari menu dinein)
     */
    public function bookingManual($meja_id = null)
    {
        // Jika tidak ada meja_id, redirect ke pilih meja
        if (!$meja_id) {
            return redirect()->route('dinein.booking')->with('error', 'Silakan pilih meja terlebih dahulu.');
        }

        // Jika session belum ada, set session
        if (!Session::has('jenis_pesanan') || !Session::has('meja_id')) {
            $meja = Meja::find($meja_id);
            if (!$meja) {
                return redirect()->route('home')->with('error', 'Meja tidak ditemukan.');
            }

            Session::put('meja_id', $meja_id);
            Session::put('jenis_pesanan', 'dinein');
            Session::put('meja_data', [
                'id' => $meja->id,
                'nomor_meja' => $meja->nomor_meja,
                'tipe_meja' => $meja->tipe_meja,
                'lantai' => $meja->lantai,
                'status' => $meja->status
            ]);
        }

        // Ambil meja dari session
        $meja_id = Session::get('meja_id');
        $meja = Meja::find($meja_id);
        
        if (!$meja) {
            Session::forget(['meja_id', 'jenis_pesanan', 'meja_data', 'notif_meja']);
            return redirect()->route('home')->with('error', 'Meja tidak ditemukan.');
        }

        // ✅ Set initial cart count untuk dine-in
        $cartCount = Cart::where('meja_id', $meja_id)
                        ->where('jenis_pesanan', 'dinein')
                        ->sum('qty');
        Session::put('cart_count_dinein', $cartCount);

        // Render view booking
        return view('cart.dinein.booking', [
            'mejaData' => $meja,
            'showNotifikasi' => false, // false untuk akses manual
            'kategoris' => Kategori::with('menus')->get(),
            'menus' => Menu::all(),
        ]);
    }

    // Method untuk menghapus session notifikasi (dipanggil via AJAX)
    public function hapusNotifMeja(Request $request)
    {
        Session::forget('notif_meja');
        return response()->json(['success' => true]);
    }

    // LandingController.php
    public function saveTakeawayCustomer(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'nomor_wa' => 'required|string|max:20',
            'tanggal_pesanan' => 'required|date',
            'waktu_pesanan' => 'required|string',
        ]);

        // Simpan ke session
        session([
            'takeaway' => [
                'nama_pelanggan' => $request->nama_pelanggan,
                'nomor_wa' => $request->nomor_wa,
                'tanggal_pesanan' => $request->tanggal_pesanan,
                'waktu_pesanan' => $request->waktu_pesanan,
            ],
            'jenis_pesanan' => 'takeaway',
            'nomor_wa' => $request->nomor_wa,
            'nama_pelanggan' => $request->nama_pelanggan,
        ]);

        // ✅ Set initial cart count untuk takeaway
        $cartCount = Cart::where('nomor_wa', $request->nomor_wa)
                        ->where('jenis_pesanan', 'takeaway')
                        ->sum('qty');
        session(['cart_count_takeaway' => $cartCount]);

        // Simpan juga ke DB pesanan (optional)
        \App\Models\Pesanan::create([
            'nama' => $request->nama_pelanggan,
            'nomor_wa' => $request->nomor_wa,
            'tanggal' => $request->tanggal_pesanan,
            'waktu' => $request->waktu_pesanan,
            'jenis_pesanan' => 'takeaway',
            'status' => 'belum bayar',
        ]);

        return redirect()->route('booking.takeaway')->with('success', 'Data pelanggan tersimpan.');
    }

    public function cart()
    {
        $jenis = Session::get('jenis_pesanan');
        $meja_id = Session::get('meja_id');
        $nomor_wa = Session::get('nomor_wa');

        if ($jenis === 'dinein' && $meja_id) {
            $cartItems = Cart::where('meja_id', $meja_id)->with('menu')->get();
            $bookingRoute = route('cart.dinein.booking.by.meja', ['meja_id' => $meja_id]);
            return view('cart.dinein.cart', compact('cartItems', 'bookingRoute'));
        } elseif ($jenis === 'takeaway' && $nomor_wa) {
            $cartItems = Session::get('cart_takeaway', []);
            $bookingRoute = route('takeaway.booking');
            return view('cart.takeaway.cart', compact('cartItems', 'bookingRoute'));
        }

        return redirect()->route('home')->with('error', 'Silakan pilih metode pemesanan.');
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menu,id',
            'qty' => 'required|integer|min:1',
            'jenis_pesanan' => 'required|in:dinein,takeaway',
        ]);

        $currentJenis = Session::get('jenis_pesanan');

        // Kosongkan cart jika berpindah jenis pesanan
        if ($currentJenis && $currentJenis !== $request->jenis_pesanan) {
            Session::forget('cart_dinein');
            Session::forget('cart_takeaway');
        }

        // Simpan/update jenis pesanan di session
        Session::put('jenis_pesanan', $request->jenis_pesanan);

        $menu = Menu::findOrFail($request->menu_id);

        // === TAKEAWAY ===
        if ($request->jenis_pesanan === 'takeaway') {
            $takeawaySession = Session::get('takeaway');

            // Validasi data session takeaway
            if (
                !$takeawaySession ||
                !isset(
                    $takeawaySession['nama_pelanggan'],
                    $takeawaySession['nomor_wa'],
                    $takeawaySession['tanggal_pesanan'],
                    $takeawaySession['waktu_pesanan']
                )
            ) {
                return back()->with('error', 'Data pelanggan takeaway belum lengkap.');
            }

            $nomorWa = $takeawaySession['nomor_wa'];
            $qty = $request->qty;

            // Cek apakah menu sudah ada di cart
            $cart = Cart::firstOrNew([
                'nomor_wa' => $nomorWa,
                'menu_id' => $menu->id,
                'jenis_pesanan' => 'takeaway',
            ]);

            $newQty = $cart->exists ? $cart->qty + $qty : $qty;

            if ($newQty > $menu->stok) {
                return back()->with('error', 'Stok menu tidak mencukupi.');
            }

            $cart->qty = $newQty;
            $cart->nama_pelanggan = $takeawaySession['nama_pelanggan'];
            $cart->save();

            // ✅ Update cart count takeaway di session
            $cartCount = Cart::where('nomor_wa', $nomorWa)
                            ->where('jenis_pesanan', 'takeaway')
                            ->sum('qty');
            session(['cart_count_takeaway' => $cartCount]);

            // ✅ Return JSON untuk AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item berhasil ditambahkan ke keranjang takeaway.',
                    'cart_count' => $cartCount
                ]);
            }

            return back()->with('success', 'Item berhasil ditambahkan ke keranjang takeaway.');
        }

        // === DINE-IN ===
        if ($request->jenis_pesanan === 'dinein') {
            $meja_id = Session::get('meja_id');
            
            if (!$meja_id) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Session meja tidak ditemukan. Silakan scan QR code meja lagi.'
                    ]);
                }
                return back()->with('error', 'Session meja tidak ditemukan. Silakan scan QR code meja lagi.');
            }

            $qty = $request->qty;

            // Cek apakah menu sudah ada di cart
            $cart = Cart::firstOrNew([
                'meja_id' => $meja_id,
                'menu_id' => $menu->id,
                'jenis_pesanan' => 'dinein',
            ]);

            $newQty = $cart->exists ? $cart->qty + $qty : $qty;

            if ($newQty > $menu->stok) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok menu tidak mencukupi.'
                    ]);
                }
                return back()->with('error', 'Stok menu tidak mencukupi.');
            }

            $cart->qty = $newQty;
            $cart->save();

            // ✅ Update cart count dine-in di session
            $cartCount = Cart::where('meja_id', $meja_id)
                            ->where('jenis_pesanan', 'dinein')
                            ->sum('qty');
            session(['cart_count_dinein' => $cartCount]);

            // ✅ Return JSON untuk AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item berhasil ditambahkan ke keranjang dine-in.',
                    'cart_count' => $cartCount
                ]);
            }

            return back()->with('success', 'Item berhasil ditambahkan ke keranjang dine-in.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis pemesanan tidak valid.'
            ]);
        }

        return back()->with('error', 'Jenis pemesanan tidak valid.');
    }
    public function getTakeawayCartCount(Request $request)
    {
        $takeawaySession = Session::get('takeaway');
        
        if (!$takeawaySession || !isset($takeawaySession['nomor_wa'])) {
            return response()->json(['cart_count' => 0]);
        }

        $nomorWa = $takeawaySession['nomor_wa'];
        $cartCount = Cart::where('nomor_wa', $nomorWa)
                        ->where('jenis_pesanan', 'takeaway')
                        ->sum('qty');

        // Update session cart count
        session(['cart_count_takeaway' => $cartCount]);

        return response()->json(['cart_count' => $cartCount]);
    }

    public function removeFromCart($id)
    {
        $meja_id = Session::get('meja_id');
        $nomor_wa = Session::get('nomor_wa');

        if ($meja_id) {
            Cart::where('id', $id)->where('meja_id', $meja_id)->delete();
        } elseif ($nomor_wa) {
            $cartTakeaway = Session::get('cart_takeaway', []);
            $cartTakeaway = array_filter($cartTakeaway, fn ($item) => $item['menu_id'] != $id);
            Session::put('cart_takeaway', $cartTakeaway);
        }

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function checkoutTakeaway()
    {
        $nama_pelanggan = Session::get('nama_pelanggan');
        $nomor_wa = Session::get('nomor_wa');
        $cartTakeaway = Session::get('cart_takeaway', []);

        if (!$nama_pelanggan || !$nomor_wa || empty($cartTakeaway)) {
            return redirect()->route('takeaway.booking')->with('error', 'Harap isi data pelanggan sebelum checkout.');
        }

        foreach ($cartTakeaway as $item) {
            Cart::create([
                'menu_id' => $item['menu_id'],
                'qty' => $item['qty'],
                'jenis_pesanan' => 'takeaway',
                'nama_pelanggan' => $nama_pelanggan,
                'nomor_wa' => $nomor_wa,
            ]);
        }

        Session::forget(['cart_takeaway', 'nama_pelanggan', 'nomor_wa', 'jenis_pesanan']);

        return redirect()->route('home')->with('success', 'Pesanan berhasil disimpan.');
    }

    // Method untuk clear semua session (optional, untuk testing)
    public function clearSession()
    {
        Session::flush();
        return redirect()->route('home')->with('success', 'Session berhasil dibersihkan.');
    }

    // Method untuk menampilkan halaman scan QR (jika diperlukan)
    public function showScanQr()
    {
        return view('scan-qr');
    }
}