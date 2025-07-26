<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cafe Saung Gemilang')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">


    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-white" href="{{ route('home') }}">
                <i class="fa fa-home"></i> Home
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="container mt-4">
        @yield('content')
    </main>

    <!-- Modal Pemilihan Tempat Duduk -->
    <div class="modal fade" id="seatSelectionModal" tabindex="-1" aria-labelledby="seatSelectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="seatSelectionModalLabel">Pilih Tempat Duduk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="seat-option" data-seat="Lesehan">
                                {{-- <img src="{{ asset('assets/img/tempat-duduk/Lesehan.jpg') }}" alt="Lesehan" class="img-fluid rounded" style="height: 150px; object-fit: cover;"> --}}
                                <p class="mt-2">Lesehan</p>
                                <button class="btn btn-outline-success check-seat" data-seat="Lesehan">
                                    <i class="fa fa-check-circle"></i> Pilih
                                </button>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="seat-option" data-seat="Meja Cafe">
                                <img src="{{ asset('assets/img/tempat-duduk/Kursi&Meja.jpg') }}" alt="Meja dan Kursi" class="img-fluid rounded" style="height: 150px; object-fit: cover;">
                                <p class="mt-2">Meja dan Kursi</p>
                                <button class="btn btn-outline-success check-seat" data-seat="Meja Cafe">
                                    <i class="fa fa-check-circle"></i> Pilih
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        $(document).ready(function () {
            let csrfToken = $('meta[name="csrf-token"]').attr("content");

            // Event delegation untuk menangani tombol pemilihan tempat duduk
            $(document).on("click", ".check-seat", function () {
                let tipeMeja = $(this).data("seat");
                $("#tableNumber").val(tipeMeja);

                let posisiTempatSelect = $("#posisi_tempat");
                posisiTempatSelect.empty();

                if (tipeMeja === "Lesehan") {
                    posisiTempatSelect.append(`<option value="Lantai 1">Lantai 1</option>`);
                } else if (tipeMeja === "Meja Cafe") {
                    posisiTempatSelect.append(`
                        <option value="Lantai 1">Lantai 1</option>
                        <option value="Lantai 2">Lantai 2</option>
                    `);
                }

                $("#seatSelectionModal").modal("hide");
            });

            // Update kuantitas item dalam keranjang
            $(document).on("click", ".update-qty", function () {
                let cartId = $(this).data("id");
                let action = $(this).data("action");

                $.ajax({
                    url: "/cart/update",
                    method: "POST",
                    data: {
                        _token: csrfToken,
                        cart_id: cartId,
                        action: action
                    },
                    success: function (response) {
                        if (response.success) {
                            $("#cart-items").html(response.cart_html);
                            $("#order-summary").html(response.order_summary);
                        }
                    },
                    error: function () {
                        Swal.fire("Error", "Terjadi kesalahan saat memperbarui keranjang", "error");
                    }
                });
            });

            // Hapus item dari keranjang dengan konfirmasi
            $(document).on("click", ".delete-cart", function () {
                let cartId = $(this).data("id");

                Swal.fire({
                    title: "Hapus Item?",
                    text: "Item ini akan dihapus dari keranjang!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/cart/destroy/" + cartId,
                            method: "DELETE",
                            data: { _token: csrfToken },
                            success: function (response) {
                                if (response.success) {
                                    $("#cart-items").html(response.cart_html);
                                    $("#order-summary").html(response.order_summary);
                                    Swal.fire("Dihapus!", "Item telah dihapus dari keranjang.", "success");
                                }
                            },
                            error: function () {
                                Swal.fire("Error", "Terjadi kesalahan saat menghapus item", "error");
                            }
                        });
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
