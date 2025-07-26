<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log; // ✅ Added missing import
use App\Models\Cart;
use App\Models\Menu;

class CartTakeawayController extends Controller
{
    public function index()
    {
        return view('cart.takeaway.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menu,id',
            'qty' => 'required|integer|min:1',
        ]);

        $takeawaySession = Session::get('takeaway');
        $jenisPesanan = Session::get('jenis_pesanan');

        if (
            !$takeawaySession ||
            !isset(
                $takeawaySession['nama_pelanggan'],
                $takeawaySession['nomor_wa'],
                $takeawaySession['tanggal_pesanan'],
                $takeawaySession['waktu_pesanan']
            ) || $jenisPesanan !== 'takeaway'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Session takeaway belum diatur dengan benar. Silakan isi data pelanggan terlebih dahulu.'
            ]);
        }

        $menuId = $request->input('menu_id');
        $qty = $request->input('qty');
        $nomorWa = $takeawaySession['nomor_wa'];

        $menu = Menu::findOrFail($menuId);

        $cart = Cart::firstOrNew([
            'nomor_wa' => $nomorWa,
            'menu_id' => $menuId,
            'jenis_pesanan' => 'takeaway',
        ]);

        if ($cart->exists) {
            if ($cart->qty + $qty > $menu->stok) {
                return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi']);
            }
            $cart->qty += $qty;
        } else {
            if ($qty > $menu->stok) {
                return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi']);
            }
            $cart->qty = $qty;
        }

        // ✅ Simpan data pelanggan dari session ke cart
        $cart->nama_pelanggan = $takeawaySession['nama_pelanggan'];
        $cart->tanggal_pesanan = $takeawaySession['tanggal_pesanan'];
        $cart->waktu_pesanan = $takeawaySession['waktu_pesanan'];

        $cart->save();

        return $this->reloadCart($nomorWa);
    }

    private function reloadCart($nomorWa)
    {
        $carts = Cart::where('nomor_wa', $nomorWa)
            ->where('jenis_pesanan', 'takeaway')
            ->with('menu')
            ->get();

        $total = $carts->sum(fn($cart) => $cart->menu->harga * $cart->qty);
        $cartCount = $carts->sum('qty');

        $cartHtml = view("cart.takeaway.cart_items", ['cartItems' => $carts])->render();
        $orderSummary = view("cart.takeaway.order_summary", [
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

    // ✅ REMOVED: Unused method updateCartCountTakeaway()

    // ✅ FIXED: Remove table validation and add better error handling
    public function update(Request $request)
    {
        try {
            $request->validate([
                'cart_id' => 'required|integer',
                'action' => 'required|in:increase,decrease',
            ]);

            $cart = Cart::find($request->input('cart_id'));
            
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan di keranjang.'
                ]);
            }

            // ✅ FIXED: Load menu relationship to check stock
            $cart->load('menu');

            $action = $request->input('action');
            
            if ($action === 'increase') {
                // Cek stok sebelum menambah
                if ($cart->qty + 1 > $cart->menu->stok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi untuk menambah item.'
                    ]);
                }
                $cart->qty += 1;
            } elseif ($action === 'decrease') {
                if ($cart->qty > 1) {
                    $cart->qty -= 1;
                } else {
                    // Jika qty = 1 dan decrease, hapus item
                    $nomorWa = $cart->nomor_wa;
                    $cart->delete();
                    return $this->reloadCart($nomorWa);
                }
            }

            $cart->save();
            
            return $this->reloadCart($cart->nomor_wa);

        } catch (\Exception $e) {
            Log::error('Cart update error: ' . $e->getMessage()); // ✅ Fixed Log usage
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui keranjang: ' . $e->getMessage()
            ]);
        }
    }

    // ✅ FIXED: Add better error handling for destroy method
    public function destroy($id)
    {
        try {
            $cart = Cart::find($id);

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan di keranjang.'
                ]);
            }

            $nomorWa = $cart->nomor_wa;
            $cart->delete();

            return $this->reloadCart($nomorWa);

        } catch (\Exception $e) {
            Log::error('Cart destroy error: ' . $e->getMessage()); // ✅ Fixed Log usage
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus item: ' . $e->getMessage()
            ]);
        }
    }

    public function getCartCount()
    {
        $takeawaySession = Session::get('takeaway', []);
        $nomorWa = $takeawaySession['nomor_wa'] ?? null;

        $count = Cart::where('nomor_wa', $nomorWa)
            ->where('jenis_pesanan', 'takeaway')
            ->sum('qty');

        return response()->json(['count' => $count]);
    }

    public function clearCart()
    {
        $takeawaySession = Session::get('takeaway', []);
        $nomorWa = $takeawaySession['nomor_wa'] ?? null;

        if ($nomorWa) {
            Cart::where('nomor_wa', $nomorWa)->where('jenis_pesanan', 'takeaway')->delete();
        }

        return response()->json(['success' => true]);
    }

    public function takeawayCart()
    {
        $takeawayData = session('takeaway', [
            'nama_pelanggan' => null,
            'nomor_wa' => null,
            'tanggal_pesanan' => null,
            'waktu_pesanan' => null,
        ]);

        $nomorWa = $takeawayData['nomor_wa'] ?? null;

        if (!$nomorWa) {
            // Redirect ke halaman form data pelanggan jika session belum lengkap
            return redirect()->route('takeaway.customer.form')
                ->with('error', 'Silakan isi data pelanggan terlebih dahulu.');
        }

        $carts = Cart::where('nomor_wa', $nomorWa)
                    ->where('jenis_pesanan', 'takeaway')
                    ->with('menu')
                    ->get();

        $total = $carts->sum(fn($cart) => $cart->menu->harga * $cart->qty);

        return view('cart.takeaway.cart', compact('carts', 'total', 'takeawayData'));
    }
}