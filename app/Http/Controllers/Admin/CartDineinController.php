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
        $jenisPesanan = $request->session()->get('jenis_pesanan', 'dinein'); // Default 'dinein' jika tidak ada session jenis_pesanan
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
        } elseif ($request->action === "decrease" && $cart->qty > 1) {
            $cart->decrement('qty');
        } else {
            $cart->delete();
        }

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

        // ✅ Tambahkan session cart count saat buka halaman cart langsung
        session(['cart_count_dinein' => $cartCount]);

        return view('cart.dinein.cart', compact('carts', 'total'));
    }

    private function reloadCart($mejaId)
    {
        $carts = Cart::where('meja_id', $mejaId)
                     ->where('jenis_pesanan', 'dinein')
                     ->with('menu')
                     ->get();

        $total = $carts->sum(fn($cart) => $cart->menu->harga * $cart->qty);
        $cartCount = $carts->sum('qty');

        // ✅ Update session cart count dine-in
        session(['cart_count_dinein' => $cartCount]);

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
            'total' => number_format($total, 0, ',', '.'),
        ]);
    }
}
