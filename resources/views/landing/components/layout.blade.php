<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>GEMILANG CAFE & SAUNG</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO -->
    <meta name="keywords" content="">
    <meta name="description" content="">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Playball&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Library CSS -->
    <link href="{{ asset('assets/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/owlcarousel/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Bootstrap & Custom -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}?v={{ time() }}" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    {{-- Navbar --}}
    @include('landing.components.navbar')

    {{-- Main Content --}}
    @yield('content')

    {{-- Footer --}}
    @include('landing.components.footer')

    {{-- Back to Top --}}
    <a href="#" class="btn btn-md-square btn-primary rounded-circle back-to-top">
        <i class="fa fa-arrow-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Library JS -->
    @php
        $libs = [
            'assets/lib/easing/easing.min.js',
            'assets/lib/waypoints/waypoints.min.js',
            'assets/lib/counterup/counterup.min.js',
            'assets/lib/lightbox/js/lightbox.min.js',
            'assets/lib/owlcarousel/owl.carousel.min.js',
            'assets/lib/wow/wow.min.js',
        ];
    @endphp
    @foreach ($libs as $lib)
        @if (file_exists(public_path($lib)))
            <script src="{{ asset($lib) }}"></script>
        @else
            <script>console.warn("File {{ $lib }} tidak ditemukan");</script>
        @endif
    @endforeach

    <!-- Instascan (QR Code) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/instascan/1.0.0/instascan.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/main.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/addtocart.js') }}?v={{ time() }}"></script>

    <!-- Init WOW.js -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (typeof WOW !== "undefined") {
                new WOW().init();
            } else {
                console.error("WOW.js not loaded");
            }
        });
    </script>
</body>
</html>
