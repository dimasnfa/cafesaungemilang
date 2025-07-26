<!-- Footer Start -->
<div class="container-fluid footer py-6 my-6 mb-0 bg-light wow bounceInUp" data-wow-delay="0.1s">
    <div class="container">
        <div class="row">

            <!-- Brand & Sosmed -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-item">
                    <h1 class="text-primary">Gemilang</h1>
                    <h2 class="text-dark">Cafe & Saung</h2>
                    <p class="lh-lg mb-4">Cafe & Saung Gemilang, tempat nyaman menikmati hidangan lezat dengan sentuhan lokal di tengah asri alam.</p>
                    <div class="footer-icon d-flex">
                        <a href="https://instagram.com/_gemilangtegal" target="_blank" class="btn custom-color btn-sm-square me-2 rounded-circle">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/6282324437584" target="_blank" class="btn custom-color btn-sm-square me-2 rounded-circle">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Custom Button Color Style -->
            <style>
                .custom-color {
                    background-color: #c6a475 !important;
                    border-color: #c6a475 !important;
                    color: #fff !important;
                }
                .custom-color:hover {
                    background-color: #b08a65 !important;
                    border-color: #b08a65 !important;
                }
                .map-iframe {
                    width: 100%;
                    height: 250px;
                    border: 0;
                    border-radius: 8px;
                }
            </style>

            <!-- Waktu Buka -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-item">
                    <h4 class="mb-4">Waktu Buka</h4>
                    <div class="d-flex flex-column align-items-start">
                        @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                            <p>- {{ $hari }} : 08.00 - 22.00</p>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-item">
                    <h4 class="mb-4">Hubungi Kami</h4>
                    <div class="d-flex flex-column align-items-start">
                        <p><i class="fa fa-map-marker-alt text-primary me-2"></i>Jl. Raya Sindang, Pagiyanten, Tegal</p>
                        <p><i class="fa fa-phone-alt text-primary me-2"></i> 0823-2443-7584</p>
                        <p><i class="fas fa-envelope text-primary me-2"></i> gemilang@gmail.com</p>
                        <p><i class="fab fa-instagram text-primary me-2"></i> @gemilang</p>
                    </div>
                </div>
            </div>

            <!-- Lokasi (Google Maps Embed) -->
            <!-- Lokasi (Google Maps Embed) -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-item">
                    <h4 class="mb-4">Lokasi</h4>
                    <div class="map-container">
                        <iframe
                            class="map-iframe"
                            width="100%"
                            height="250"
                            style="border:0; border-radius: 8px;"
                            loading="lazy"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://maps.google.com/maps?hl=id&q=-6.947866,109.104365&z=16&output=embed">
                        </iframe>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
<!-- Footer End -->

<!-- Copyright -->
<div class="container-fluid copyright bg-dark py-4">
    <div class="container text-center">
        <span class="text-light">
            <i class="fas fa-copyright text-light me-2"></i>
            Gemilang Cafe & Saung, All rights reserved.
        </span>
    </div>
</div>
