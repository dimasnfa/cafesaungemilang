{{-- ✅ FIXED: Template untuk menampilkan item cart atau pesan kosong --}}
@if(count($cartItems) > 0)
    @foreach ($cartItems as $cart)
    <tr data-id="{{ $cart->id }}">
        <td>{{ $cart->menu->nama_menu }}</td>
        <td>Rp {{ number_format($cart->menu->harga, 0, ',', '.') }}</td>
        <td>
            <div class="qty-container d-flex justify-content-center align-items-center">
                <button type="button" class="btn btn-sm btn-outline-secondary btn-qty" data-cart-id="{{ $cart->id }}" data-action="decrease">-</button>
                <!-- ✅ FIXED: Removed readonly, increased width, added max attribute -->
                <input type="number" 
                        value="{{ $cart->qty }}" 
                        min="1" 
                        max="{{ $cart->menu->stok }}" 
                        class="form-control text-center mx-2 qty-input qty-direct-input" 
                        style="width: 70px;" 
                        data-cart-id="{{ $cart->id }}">
                <button type="button" class="btn btn-sm btn-outline-secondary btn-qty" data-cart-id="{{ $cart->id }}" data-action="increase">+</button>
            </div>
        </td>
        <td>Rp <span class="cart-total">{{ number_format($cart->menu->harga * $cart->qty, 0, ',', '.') }}</span></td>
        <td>
            <button class="btn btn-danger btn-sm btn-delete-cart" data-cart-id="{{ $cart->id }}">Hapus</button>
        </td>
    </tr>
    @endforeach
@else
    {{-- ✅ FIXED: Tampilkan pesan jika cart kosong --}}
    <tr>
        <td colspan="5" class="text-center text-muted py-4">
            <p class="mb-0">Keranjang kosong. Silakan pilih menu terlebih dahulu.</p>
        </td>
    </tr>
@endif