<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <!-- Logo -->
        <a href="/" class="navbar-brand d-flex align-items-center">
            <img src="{{ asset('assets/img/banner/icon-gemilang.png') }}" alt="Gemilang Logo" class="me-2" style="height: 70px;">
            <h1 class="text-primary fw-bold mb-0">
                <span class="d-block">Gemilang</span>
                <span class="text-dark d-block" style="font-size: 0.8em;">Cafe & Saung</span>
            </h1>
        </a>

        <!-- Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="fa fa-bars text-primary"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarCollapse">
            <ul class="navbar-nav">
                @if(session('meja') && Request::is('booking/*'))
                <li class="nav-item">
                    <a href="{{ url('/booking/' . session('meja')) }}" class="nav-link {{ Request::is('booking/*') ? 'active' : '' }}">
                        Meja {{ session('meja') }}
                    </a>
                </li>
            @endif

            
            </ul>
        </div>

        <!-- Bagian Kanan Navbar (Keranjang) -->
        <div class="d-flex align-items-center">
            @if(!Request::is('/'))
                @if(session('meja_id') && !Request::is('takeaway/*'))
                    <!-- Keranjang Dine-In -->
                    <a href="{{ route('cart.dinein.cart') }}" class="position-relative me-3">
                        <i class="fa fa-shopping-cart fa-2x"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ session('cart_count_dinein', 0) }}
                        </span>
                    </a>
                @elseif(Request::is('takeaway/*'))
                    <!-- Keranjang Takeaway -->
                    <a href="{{ route('cart.takeaway.cart') }}" class="position-relative me-3">
                        <i class="fa fa-shopping-cart fa-2x"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ session('cart_count_takeaway', 0) }}
                        </span>
                    </a>
                @endif
            @endif
        </div>
    </div>
</nav>

<!-- Modal untuk Takeaway -->
{{-- <div class="modal fade" id="takeawayModal" tabindex="-1" aria-labelledby="takeawayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="takeawayModalLabel">Informasi Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="takeawayForm">
                    <div class="mb-3">
                        <label for="namaPelanggan" class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="namaPelanggan" required>
                    </div>
                    <div class="mb-3">
                        <label for="nomorWA" class="form-label">Nomor WhatsApp</label>
                        <input type="tel" class="form-control" id="nomorWA" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}

@if(Request::is('takeaway*'))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const checkoutButton = document.querySelector("#checkoutButton");
            const takeawayForm = document.querySelector("#takeawayForm");

            if (checkoutButton) {
                checkoutButton.addEventListener("click", function (event) {
                    event.preventDefault();
                    let takeawayModal = new bootstrap.Modal(document.getElementById('takeawayModal'));
                    takeawayModal.show();
                });
            }

            if (takeawayForm) {
                takeawayForm.addEventListener("submit", function (event) {
                    event.preventDefault();
                    let namaPelanggan = document.getElementById("namaPelanggan").value;
                    let nomorWA = document.getElementById("nomorWA").value;

                    if (!namaPelanggan || !nomorWA) {
                        alert("Harap isi Nama Pelanggan dan Nomor WhatsApp sebelum melanjutkan pembayaran.");
                        return;
                    }

                    sessionStorage.setItem("namaPelanggan", namaPelanggan);
                    sessionStorage.setItem("nomorWA", nomorWA);

                    window.location.href = "{{ route('cart.takeaway.cart') }}";
                });
            }
        });
    </script>
@endif
