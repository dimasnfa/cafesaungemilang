<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Cart;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
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

public function booking($meja_id = null, Request $request)
{
    // Jika parameter from_qr ada dan yes, simpan session
    if ($request->has('from_qr') && $request->input('from_qr') === 'yes') {
        // Validasi meja_id ada
        $meja = Meja::find($meja_id);
        if (!$meja) {
            return redirect()->route('home')->with('error', 'Meja tidak ditemukan.');
        }

        // Set session
       Session::put('meja_id', $meja_id);
       Session::put('jenis_pesanan', 'dinein');
       Session::flash('notif_meja', true); // ✅ hanya untuk popup 1x

        // Redirect ke URL tanpa query string supaya from_qr tidak muncul
      return redirect()->route('cart.dinein.booking.by.meja', ['meja_id' => $meja_id]);
    }

    // Jika session belum ada (misal akses langsung tanpa from_qr)
    if (!Session::has('jenis_pesanan') || !Session::has('meja_id')) {
        return redirect()->route('scan.qr')->with('error', 'Akses tidak valid atau session habis.');
    }

    // Ambil meja dari session
    $meja = Meja::find(Session::get('meja_id'));
    if (!$meja) {
        return redirect()->route('home')->with('error', 'Meja tidak ditemukan.');
    }

    // Render view booking dengan data meja
    return view('cart.dinein.booking', [
        'mejaData' => $meja,
        'kategoris' => Kategori::with('menus')->get(),
        'menus' => Menu::all(),
    ]);
}




public function scanQr(Request $request)
{
    $mejaId = $request->input('meja_id');

    if (!$mejaId) {
        return redirect('/')->with('error', 'Meja tidak ditemukan.');
    }

    return redirect()->route('cart.dinein.booking.by.meja', [
        'meja' => $mejaId,
        'from_qr' => 'yes'
    ]);
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
        'nomor_wa' => $request->nomor_wa, // ✅ penting untuk cart & checkout
        'nama_pelanggan' => $request->nama_pelanggan, // ✅ untuk tampilan cart
    ]);

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
            $bookingRoute = route('booking.by.meja', ['meja' => $meja_id]);
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

        return back()->with('success', 'Item berhasil ditambahkan ke keranjang takeaway.');
    }

    // === DINE-IN ===
    // Untuk dine-in, arahkan ke CartDineinController atau implementasikan di sini
    return back()->with('error', 'Jenis pemesanan dine-in belum diimplementasikan di method ini.');
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
}
