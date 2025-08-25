{{-- ✅ FIXED: Order summary dengan handling untuk cart kosong --}}
@if(count($carts) > 0)
<ul class="list-group">
    @foreach ($carts as $cart)
    <li class="list-group-item d-flex justify-content-between">
        <span>{{ $cart->menu->nama_menu }} x{{ $cart->qty }}</span>
        <span>Rp {{ number_format($cart->menu->harga * $cart->qty, 0, ',', '.') }}</span>
    </li>
    @endforeach
    <li class="list-group-item d-flex justify-content-between">
        <strong>Total Harga:</strong>
        <strong id="totalPrice">Rp {{ number_format($total, 0, ',', '.') }}</strong>
    </li>
</ul>
@else
{{-- ✅ FIXED: Tampilkan pesan ketika tidak ada item --}}
<ul class="list-group">
    <li class="list-group-item text-center text-muted">
        Tidak ada item di keranjang
    </li>
    <li class="list-group-item d-flex justify-content-between">
        <strong>Total Harga:</strong>
        <strong id="totalPrice">Rp 0</strong>
    </li>
</ul>
@endif