<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Menu;

class CartDineinController extends Controller
{
    public function index(Request $request)
    {
        $mejaId = $request->session()->get('meja_id');
        $jenisPesanan = $request->session()->get('jenis_pesanan', 'dinein');
        $carts = Cart::where('meja_id', $mejaId)
                     ->where('jenis_pesanan', $jenisPesanan)
                     ->with('menu')
                     ->get();
        $total = $carts->sum(fn($cart) => $cart->menu->harga * $cart->qty);

        return view('cart.dinein.cart', compact('carts', 'total'));
    }

    public function booking($mejaId)
    {
        session(['meja_id' => $mejaId, 'jenis_pesanan' => 'dinein']);
        return redirect()->route('cart.dinein.cart');
    }

    public function showBookingPage(Request $request)
    {
        if (!$request->session()->has('jenis_pesanan')) {
            $request->session()->put('jenis_pesanan', 'dinein');
        }

        $fromQR = $request->query('from_qr') === 'yes';

        return view('booking', compact('fromQR'));
    }

    public function store(Request $request)
    {
        $mejaId = session('meja_id');
        if (!$mejaId) {
            return response()->json(['success' => false, 'message' => 'Meja tidak terdeteksi.']);
        }

        $menu = Menu::findOrFail($request->menu_id);

        if ($menu->stok < 1) {
            return response()->json(['success' => false, 'message' => 'Stok habis']);
        }

        $cart = Cart::firstOrNew([
            'menu_id' => $menu->id,
            'meja_id' => $mejaId,
            'jenis_pesanan' => 'dinein'
        ]);

        if ($cart->exists) {
            if ($cart->qty < $menu->stok) {
                $cart->increment('qty');
            } else {
                return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi']);
            }
        } else {
            $cart->qty = 1;
            $cart->save();
        }

        return $this->reloadCart($mejaId);
    }

    public function scanQR($meja_id)
    {
        session(['meja_id' => $meja_id, 'jenis_pesanan' => 'dinein']);
        return redirect()->route('booking.by.meja', ['meja' => $meja_id]);
    }

    public function update(Request $request)
    {
        $cart = Cart::findOrFail($request->cart_id);
        $menu = Menu::findOrFail($cart->menu_id);

        if ($request->action === "increase") {
            if ($cart->qty < $menu->stok) {
                $cart->increment('qty');
            } else {
                return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi']);
            }
        } elseif ($request->action === "decrease") {
            if ($cart->qty > 1) {
                $cart->decrement('qty');
            } else {
                // Jika qty = 1 dan decrease, hapus item dari cart
                $cart->delete();
            }
        }

        return $this->reloadCart($cart->meja_id);
    }

    // ✅ PERBAIKAN: Method baru untuk update quantity secara langsung
    public function updateDirect(Request $request)
    {
        $cart = Cart::findOrFail($request->cart_id);
        $menu = Menu::findOrFail($cart->menu_id);
        $newQty = (int) $request->qty;

        // Validasi quantity
        if ($newQty < 1) {
            return response()->json(['success' => false, 'message' => 'Quantity tidak valid']);
        }

        if ($newQty > $menu->stok) {
            return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi']);
        }

        // Update quantity
        $cart->qty = $newQty;
        $cart->save();

        return $this->reloadCart($cart->meja_id);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);
        $mejaId = $cart->meja_id;
        $cart->delete();

        return $this->reloadCart($mejaId);
    }

    public function clearCart(Request $request)
    {
        $mejaId = $request->session()->get('meja_id');
        Cart::where('meja_id', $mejaId)
            ->where('jenis_pesanan', 'dinein')
            ->delete();

        return $this->reloadCart($mejaId);
    }

    public function dineinCart(Request $request)
    {
        $mejaId = $request->session()->get('meja_id');
        $carts = Cart::where('meja_id', $mejaId)
                     ->where('jenis_pesanan', 'dinein')
                     ->with('menu')
                     ->get();

        $total = $carts->sum(fn($cart) => $cart->menu->harga * $cart->qty);
        $cartCount = $carts->sum('qty');

        // Update session cart count saat buka halaman cart langsung
        session(['cart_count_dinein' => $cartCount]);

        return view('cart.dinein.cart', compact('carts', 'total'));
    }

    // Method untuk get cart count via AJAX
    public function getCartCount(Request $request)
    {
        $mejaId = $request->session()->get('meja_id');
        
        if (!$mejaId) {
            return response()->json(['cart_count' => 0]);
        }

        $cartCount = Cart::where('meja_id', $mejaId)
                        ->where('jenis_pesanan', 'dinein')
                        ->sum('qty');

        // Update session cart count
        session(['cart_count_dinein' => $cartCount]);

        return response()->json(['cart_count' => $cartCount]);
    }

    // Method untuk AJAX add to cart dari booking page
    public function ajaxAddToCart(Request $request)
    {
        $mejaId = session('meja_id');
        if (!$mejaId) {
            return response()->json([
                'success' => false, 
                'message' => 'Meja tidak terdeteksi.',
                'cart_count' => 0
            ]);
        }

        $menu = Menu::find($request->menu_id);
        if (!$menu) {
            return response()->json([
                'success' => false, 
                'message' => 'Menu tidak ditemukan.',
                'cart_count' => 0
            ]);
        }

        if ($menu->stok < 1) {
            return response()->json([
                'success' => false, 
                'message' => 'Stok habis',
                'cart_count' => $this->getCurrentCartCount($mejaId)
            ]);
        }

        $cart = Cart::firstOrNew([
            'menu_id' => $menu->id,
            'meja_id' => $mejaId,
            'jenis_pesanan' => 'dinein'
        ]);

        if ($cart->exists) {
            if ($cart->qty < $menu->stok) {
                $cart->increment('qty');
                $message = $menu->nama_menu . ' berhasil ditambahkan ke keranjang';
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Stok tidak mencukupi',
                    'cart_count' => $this->getCurrentCartCount($mejaId)
                ]);
            }
        } else {
            $cart->qty = 1;
            $cart->save();
            $message = $menu->nama_menu . ' berhasil ditambahkan ke keranjang';
        }

        $newCartCount = $this->getCurrentCartCount($mejaId);
        
        // Update session cart count
        session(['cart_count_dinein' => $newCartCount]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'cart_count' => $newCartCount,
            'menu_name' => $menu->nama_menu
        ]);
    }

    // ✅ PERBAIKAN: Method reloadCart yang diperbaiki dengan data lebih lengkap
    private function reloadCart($mejaId)
    {
        $carts = Cart::where('meja_id', $mejaId)
                     ->where('jenis_pesanan', 'dinein')
                     ->with('menu')
                     ->get();

        $total = $carts->sum(fn($cart) => $cart->menu->harga * $cart->qty);
        $cartCount = $carts->sum('qty');

        // Update session cart count dine-in
        session(['cart_count_dinein' => $cartCount]);

        // Prepare cart data for localStorage sync
        $cartData = $carts->map(function ($cart) {
            return [
                'id' => $cart->id,
                'menu_id' => $cart->menu_id,
                'nama_menu' => $cart->menu->nama_menu,
                'harga' => $cart->menu->harga,
                'qty' => $cart->qty,
                'total' => $cart->menu->harga * $cart->qty
            ];
        })->toArray();

        $cartHtml = view("cart.dinein.cart_items", ['cartItems' => $carts])->render();
        $orderSummary = view("cart.dinein.order_summary", [
            'carts' => $carts,
            'total' => $total,
        ])->render();

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'cart_html' => $cartHtml,
            'order_summary' => $orderSummary,
            'total' => $total, // Raw number untuk perhitungan
            'formatted_total' => number_format($total, 0, ',', '.'), // Formatted untuk display
            'cart_data' => $cartData, // Data untuk localStorage
            'message' => $cartCount > 0 ? 'Keranjang berhasil diperbarui' : 'Keranjang kosong'
        ]);
    }

    // Helper method untuk mendapatkan cart count saat ini
    private function getCurrentCartCount($mejaId)
    {
        return Cart::where('meja_id', $mejaId)
                  ->where('jenis_pesanan', 'dinein')
                  ->sum('qty');
    }
}