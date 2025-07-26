@extends('landing.components.layout')

@section('title', 'Home - Gemilang Cafe')

@section('content')

<div class="container-fluid bg-light py-6 my-6 mt-0">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-7 col-md-12">
                <br>
                <br>
                <h1 class="display-1 mb-4 animated bounceInDown">
                    Selamat Datang
                    <div><span style="color: #bb922a; font-size: 70px;">Mau makan apa hari ini?</span></div>
                </h1>
            </div>
            <div class="col-lg-5 col-md-12">
                <img src="{{ asset('assets/img/hero.png') }}" class="img-fluid rounded animated zoomIn" alt="Hero Image">
            </div>
        </div>
    </div>
</div>

<style>
    .order-container {
        border: 3px solid rgb(199, 154, 9);
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        text-align: center;
        margin-bottom: 50px;
    }

    .scanner-container {
        border: 3px solid #bb922a;
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        text-align: center;
        margin-top: 20px;
        margin-bottom: 100px;
    }

    #reader {
        width: 100%;
        max-width: 320px;
        height: auto;
        margin: 0 auto;
        position: relative;
    }

    .scanner-frame {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 200px;
        height: 200px;
        transform: translate(-50%, -50%);
        border: 4px solid rgba(255, 0, 0, 0.7);
        border-radius: 10px;
        animation: scanning 1.5s infinite alternate ease-in-out;
    }

    @keyframes scanning {
        from {
            border-color: rgba(255, 0, 0, 0.7);
        }
        to {
            border-color: rgba(0, 255, 0, 0.7);
        }
    }
</style>

<div class="d-flex flex-column align-items-center vh-100">
    <div class="p-4 order-container w-100" style="max-width: 500px;">
        <h2 class="mb-4">Pilih Metode Pemesanan</h2>
        <div class="d-grid gap-3">
            <button id="scanQRBtn" class="btn btn-lg btn-primary py-3 w-100">
                <i class="fas fa-qrcode me-2"></i> Makan di Tempat
            </button>
            <a href="{{ route('takeaway.customer.form') }}" class="btn btn-lg btn-success py-3 w-100">
                <i class="fas fa-shopping-bag me-2"></i> Takeaway
            </a>
        </div>
    </div>

    <div id="qrReaderContainer" class="scanner-container w-100 d-none" style="max-width: 500px;">
        <h4 class="mb-3">Scan QR Code</h4>
        <div id="reader"></div>
        <p id="scanMessage" class="text-muted mt-2">Arahkan kamera ke QR Code...</p>
        <p id="scan-result" class="mt-3 text-success"></p>
        <button id="closeScannerBtn" class="btn btn-danger mt-3">Tutup Scanner</button>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let scannerContainer = document.getElementById('qrReaderContainer');
    let scanQRBtn = document.getElementById('scanQRBtn');
    let closeScannerBtn = document.getElementById('closeScannerBtn');
    let scanner;

    scanQRBtn.addEventListener('click', function () {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                stream.getTracks().forEach(track => track.stop());
                startScanner();
            })
            .catch(function (err) {
                console.error("Akses kamera ditolak:", err);
                document.getElementById("scanMessage").innerText = "Izin kamera ditolak! Silakan izinkan akses kamera di pengaturan browser.";
            });
    });

    function startScanner() {
        scannerContainer.classList.remove('d-none');
        scannerContainer.scrollIntoView({ behavior: "smooth" });

        if (scanner) {
            scanner.clear().catch(err => console.error("Error reset scanner:", err));
        }

        scanner = new Html5Qrcode("reader");
        scanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            onScanSuccess,
            function (error) {
                console.warn("Gagal scan QR: ", error);
            }
        ).catch(err => {
            console.error("Scanner gagal dimulai:", err);
            document.getElementById("scanMessage").innerText = "⚠ Kamera tidak dapat diakses!";
        });
    }

    closeScannerBtn.addEventListener('click', function () {
        scannerContainer.classList.add('d-none');
        if (scanner) {
            scanner.stop().catch(err => console.warn("Error stopping scanner: ", err));
        }
    });

    function onScanSuccess(decodedText, decodedResult) {
    console.log("QR Code Terdeteksi:", decodedText);
    document.getElementById("scan-result").innerText = "✅ QR Code: " + decodedText;

    let finalUrl;

    if (/^\d+$/.test(decodedText)) {
        finalUrl = `${window.location.origin.replace(/\/$/, '')}/dinein/booking/${decodedText}?from_qr=yes`;
    } else if (/^https?:\/\/.+\/dinein\/booking\/\d+$/i.test(decodedText)) {
        const url = new URL(decodedText);
        url.searchParams.set('from_qr', 'yes');
        finalUrl = url.toString();
    } else {
        document.getElementById("scanMessage").innerText = "QR Code tidak valid!";
        return;
    }

    setTimeout(() => window.location.href = finalUrl, 1500);
}

</script>
@endsection
