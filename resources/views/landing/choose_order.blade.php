@extends('landing.components.layout')

@section('title', 'Home - Gemilang Cafe')

@section('content')
    <div class="container">
        

    <!-- Hero Start -->
    <div class="container-fluid bg-light py-6 my-6 mt-0">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7 col-md-12">
                    <h1 class="display-1 mb-4 animated bounceInDown">
                        Selamat Datang
                        <div><span style="color: #bb922a; font-size: 70px;">Mau makan apa hari ini?</span></div>
                    </h1>
                </div>
                <div class="col-lg-5 col-md-12">
                    <img src="assets/img/hero.png" class="img-fluid rounded animated zoomIn" alt="">
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- Pilihan Order -->
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="text-center p-4 bg-white rounded-3 shadow-lg order-container w-100" style="max-width: 500px;">
            <h2 class="mb-4">Pilih Metode Pemesanan</h2>
            <div class="d-grid gap-3">
                <button id="scanQRBtn" class="btn btn-lg btn-primary py-3 w-100">
                    <i class="fas fa-qrcode me-2"></i>Makan di Tempat
                </button>
                <a href="{{ route('takeaway.booking') }}" class="btn btn-lg btn-success py-3 w-100">
                    <i class="fas fa-shopping-bag me-2"></i> Takeaway
                </a>
            </div>
            <!-- Area Scanner -->
            <video id="preview" class="mt-3 d-none w-100 rounded" style="max-width: 400px;"></video>
            <p id="scanMessage" class="text-muted mt-2 d-none">Arahkan kamera ke QR Code...</p>
        </div>
    </div>

    <!-- Script QR Scanner -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/instascan/1.0.0/instascan.min.js"></script>
    <script>
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });

        document.getElementById('scanQRBtn').addEventListener('click', function () {
            navigator.mediaDevices.getUserMedia({ video: true }) // Minta izin kamera
                .then(function (stream) {
                    document.getElementById('preview').classList.remove('d-none');
                    document.getElementById('scanMessage').classList.remove('d-none');
                    
                    Instascan.Camera.getCameras().then(function (cameras) {
                        if (cameras.length > 0) {
                            scanner.start(cameras[0]);
                        } else {
                            alert('Kamera tidak ditemukan!');
                        }
                    }).catch(function (e) {
                        console.error(e);
                        alert('Gagal mengakses kamera: ' + e.message);
                    });

                    scanner.addListener('scan', function (content) {
                        window.location.href = "{{ route('dinein.booking') }}?qr=" + encodeURIComponent(content);
                    });

                })
                .catch(function (err) {
                    console.error('Izin kamera ditolak!', err);
                    alert('Izin kamera ditolak! Silakan izinkan akses kamera di browser.');
                });
        });
    </script>

@endsection
