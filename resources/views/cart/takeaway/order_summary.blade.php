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
