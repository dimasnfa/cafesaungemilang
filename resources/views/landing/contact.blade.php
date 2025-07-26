@extends('landing.components.layout')

@section('title', 'Home - Gemilang Cafe')

@section('content')


        <!-- Modal Search Start -->
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Search End -->


        <!-- Hero Start -->
        <div class="container-fluid py-6 my-6 mt-0" style="background-image: url('assets/img/banner/banner-gemilang.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container text-center animated bounceInDown" style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px;">
                <h1 class="display-1 mb-4">Contact</h1>
                <ol class="breadcrumb justify-content-center mb-0 animated bounceInDown">
                    <li class="breadcrumb-item"><a href="#">Contact</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item text-dark" aria-current="page">Contact</li>
                </ol>
            </div>
        </div>
        <!-- Hero End -->


        <!-- Contact Start -->
        <div class="container-fluid contact py-6 wow bounceInUp" data-wow-delay="0.1s">
            <div class="container">
                <div class="p-5 bg-light rounded contact-form">
                    <div class="row g-4">
                        <div class="col-12">
                            <small class="d-inline-block fw-bold text-dark text-uppercase bg-light border border-primary rounded-pill px-4 py-1 mb-3">Get in touch</small>
                            <h1>HUBUNGI KAMI JIKA</h1>
                            <h2>ADA PERMASALAHAN!</h2>
                        </div>
                        
                        <!-- Formulir Kontak -->
                        <div class="col-md-6 col-lg-7">
                            <p class="mb-4">Isi informasi pesanan Anda, kami akan merespon secepatnya.</p>
                            <form id="contactForm">
                                <input type="text" id="name" class="w-100 form-control p-3 mb-4 border-primary bg-light" placeholder="Nama Anda" required>
                                <input type="text" id="contact" class="w-100 form-control p-3 mb-4 border-primary bg-light" placeholder="Email atau Nomor WA" required>
                                <textarea id="message" class="w-100 form-control mb-4 p-3 border-primary bg-light" rows="4" cols="10" placeholder="Pesan atau masukan" required></textarea>
                                
                                <!-- Tombol Pengiriman -->
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-success form-control me-2 p-3 border-primary bg-success rounded-pill" onclick="sendToWhatsApp()">Kirim via WhatsApp</button>
                                    <button type="button" class="btn btn-primary form-control ms-2 p-3 border-primary bg-primary rounded-pill" onclick="sendToEmail()">Kirim via Email</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Informasi Kontak -->
                        <div class="col-md-6 col-lg-5">
                            <div>
                                <div class="d-inline-flex w-100 border border-primary p-4 rounded mb-4">
                                    <i class="fas fa-map-marker-alt fa-2x text-primary me-4"></i>
                                    <div>
                                        <h4>Alamat</h4>
                                        <p>Jl. Raya Sindang, Sawah, Pagiyanten, Kec. Adiwerna, Kabupaten Tegal, Jawa Tengah, 52451</p>
                                    </div>
                                </div>
                                <div class="d-inline-flex w-100 border border-primary p-4 rounded mb-4">
                                    <i class="fas fa-envelope fa-2x text-primary me-4"></i>
                                    <div>
                                        <h4>Mail Us</h4>
                                        <p class="mb-2">gemilang@gmail.com</p>
                                    </div>
                                </div>
                                <div class="d-inline-flex w-100 border border-primary p-4 rounded">
                                    <i class="fa fa-phone-alt fa-2x text-primary me-4"></i>
                                    <div>
                                        <h4>Telephone</h4>
                                        <p class="mb-2">089517277732</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->

        <script>
        function sendToWhatsApp() {
            const name = document.getElementById('name').value;
            const message = document.getElementById('message').value;
            const whatsappNumber = "6289517277732";
            const whatsappLink = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(`Halo, saya ${name}. ${message}`)}`;
            window.open(whatsappLink, '_blank');
        }

        function sendToEmail() {
            const name = document.getElementById('name').value;
            const message = document.getElementById('message').value;
            const emailAddress = "gemilang@gmail.com";
            const subject = `Pesan dari ${name}`;
            const mailtoLink = `mailto:${emailAddress}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(message)}`;
            window.location.href = mailtoLink;
        }
        </script>

        <!-- END ABOUT ALL -->

