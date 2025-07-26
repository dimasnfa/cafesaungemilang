@extends('landing.components.layout')

@section('title', 'Home - Gemilang Cafe')

@section('content')
    
             <!-- Modal Search Start -->
             <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content rounded-0">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body d-flex align-items-center">
                            <div class="input-group w-75 mx-auto d-flex">
                                <input type="search" class="form-control bg-transparent p-3" placeholder="keywords" aria-describedby="search-icon-1">
                                <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Search End -->
    
        <!-- Hero Start -->
        <div class="container-fluid py-6 my-6 mt-0" style="background-image: url('assets/img/banner/banner-gemilang.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container text-center animated bounceInDown" style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px;">
                <h1 class="display-1 mb-4">Menu</h1>
                <ol class="breadcrumb justify-content-center mb-0 animated bounceInDown">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item text-dark" aria-current="page">Menu</li>
                </ol>
            </div>
        </div>
        <!-- Hero End -->



        @section('content')

              <div class="container-fluid menu bg-light py-6 my-6">
                <div class="container">
                    <div class="text-center wow bounceInUp" data-wow-delay="0.1s">
                        <small class="d-inline-block fw-bold text-dark text-uppercase bg-light border border-primary rounded-pill px-4 py-1 mb-3">Menu Gemilang</small>
                        <h1 class="display-5 mb-5">Menu Favorit Gemilang Café & Saung</h1>
                    </div>
                    <div class="tab-class text-center">
                        <ul class="nav nav-pills d-inline-flex justify-content-center mb-5 wow bounceInUp" data-wow-delay="0.1s">
                            <li class="nav-item p-2">
                                <a class="d-flex py-2 mx-2 border border-primary bg-white rounded-pill active" data-bs-toggle="pill" href="#tab-6">
                                    <span class="text-dark" style="width: 150px;">Makanan</span>
                                </a>
                            </li>
                            <li class="nav-item p-2">
                                <a class="d-flex py-2 mx-2 border border-primary bg-white rounded-pill" data-bs-toggle="pill" href="#tab-7">
                                    <span class="text-dark" style="width: 150px;">Nasi dan Mie</span>
                                </a>
                            </li>
                            <li class="nav-item p-2">
                                <a class="d-flex py-2 mx-2 border border-primary bg-white rounded-pill" data-bs-toggle="pill" href="#tab-8">
                                    <span class="text-dark" style="width: 150px;">Minuman</span>
                                </a>
                            </li>
                            <li class="nav-item p-2">
                                <a class="d-flex py-2 mx-2 border border-primary bg-white rounded-pill" data-bs-toggle="pill" href="#tab-9">
                                    <span class="text-dark" style="width: 150px;">Menu Paket</span>
                                </a>
                            </li>
                            <li class="nav-item p-2">
                                <a class="d-flex py-2 mx-2 border border-primary bg-white rounded-pill" data-bs-toggle="pill" href="#tab-10">
                                    <span class="text-dark" style="width: 150px;">Aneka Snack</span>
                                </a>
                            </li>
                        </ul>
    
                        
                        <div class="tab-content">
                            <div id="tab-6" class="tab-pane fade show p-0 active">
                                <div class="row g-4">
                                    <h1>Olahan Ayam</h1>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.1s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/nasi.png" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Nasi</h4>
                                                    <h4 class="text-primary">Rp.5.000</h4>
                                                </div>
                                                <p class="mb-0">.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.2s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/ayamgoreng.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Ayam Goreng</h4>
                                                    <h4 class="text-primary">Rp.30.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.3s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/ayambakar.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Ayam Bakar</h4>
                                                    <h4 class="text-primary">Rp.30.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.3s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/ayamentega.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Ayam Mentega</h4>
                                                    <h4 class="text-primary">Rp.35.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.4s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/ayamladahitam.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Ayam Lada Hitam</h4>
                                                    <h4 class="text-primary">Rp.35.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.5s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/ayamlombokijo.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Ayam Lombok Ijo</h4>
                                                    <h4 class="text-primary">Rp.35.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.6s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/ayamasammanis.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Ayam Asam Manis</h4>
                                                    <h4 class="text-primary">Rp.35.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.7s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/ayamsaospadang.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Ayam Saos Padang</h4>
                                                    <h4 class="text-primary">Rp.35.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.8s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/ayamricarica.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Ayam Rica-Rica</h4>
                                                    <h4 class="text-primary">Rp.35.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.6s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/sopayam.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Sop Ayam</h4>
                                                    <h4 class="text-primary">Rp.35.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.6s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/garangasem.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Garang Asem Ayam</h4>
                                                    <h4 class="text-primary">Rp.35.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
    
                                    <h1> Sambal </h1>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.6s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/sambalgeprek.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Sambal Geprek</h4>
                                                    <h4 class="text-primary">Rp.5.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.6s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/sambalmentah.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Sambal Mentah</h4>
                                                    <h4 class="text-primary">Rp.5.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.6s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/sambalpecak.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Sambal Pecak</h4>
                                                    <h4 class="text-primary">Rp.5.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.6s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/sambalterasi.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Sambal Terasi</h4>
                                                    <h4 class="text-primary">Rp.5.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.6s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/sambalbawang.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Sambal Bawang</h4>
                                                    <h4 class="text-primary">Rp.5.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.6s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/sambalijo.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Sambal Ijo</h4>
                                                    <h4 class="text-primary">Rp.5.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 wow bounceInUp" data-wow-delay="0.6s">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/makanan/sambaldabu.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Sambal Dabu-Dabu</h4>
                                                    <h4 class="text-primary">Rp.5.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
    
                            <!-- NASI & MIE GEMILANG -->
    
                            <div id="tab-7" class="tab-pane fade show p-0">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/nasi-dan-mie/nasigorenggemilang.png" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Nasi Goreng Gemilang</h4>
                                                    <h4 class="text-primary">Rp.25.0000</h4>
                                                </div>
                                                <p class="mb-0">Lezatnya perpaduan rasa yang bikin nagih</p>
                                            </div>
                                        </div>
                                    </div>   
    
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/nasi-dan-mie/kwetiau.png" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Kwetiau Goreng</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Kenikmatan tiap gigitan, selalu memuaskan.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/nasi-dan-mie/nasigorengayam.png" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Nasi Goreng Ayam</h4>
                                                    <h4 class="text-primary">Rp.20.000</h4>
                                                </div>
                                                <p class="mb-0">Sensasi gurihnya bikin ketagihan.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/nasi-dan-mie/miegoreng.png" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Mie Goreng</h4>
                                                    <h4 class="text-primary">Rp.20.000</h4>
                                                </div>
                                                <p class="mb-0">Kelezatan sederhana yang selalu pas di lidah.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/nasi-dan-mie/nasigorengseafood.png" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Nasi Goreng Seafood</h4>
                                                    <h4 class="text-primary">Rp.25.000</h4>
                                                </div>
                                                <p class="mb-0">Rasa Kenikmatan Seafood yang menggoda dalam setiap suapan.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/nasi-dan-mie/indomierebus.png" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Indomie Rebus</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Kenyamanan dalam setiap suapan, sempurna untuk menemani hari</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/nasi-dan-mie/indomiegoreng.png" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Indomie Goreng+toping</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Kombinasi gurih dan renyah dengan topping pilihan</p>
                                            </div>
                                        </div>
                                    </div>
    
                                     <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/nasi-dan-mie/kwetiaurebus.png" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Kwetiau Rebus</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Hangat dan mengenyangkan, sempurna untuk suasana santai.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
    
                            <!-- MINUMAN -->
    
                            <div id="tab-8" class="tab-pane fade show p-0">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/alpukat.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Jus Alpukat</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Minuman segar dengan rasa alpukat yang creamy dan nikmat.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/mangga.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Jus Mangga</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Jus mangga manis dan menyegarkan, sempurna untuk melepas dahaga</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/apel.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Jus Apel</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Jus apel segar dengan rasa alami yang sehat dan lezat.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/melon.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Jus Melon</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/strawberry.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Jus Strawberry</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Minuman manis dengan rasa segar khas buah strawberry</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/fibar.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Jus Fibar</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">us kaya serat untuk kesehatan tubuh dan pencernaan..</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/jeruk.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Jus Jeruk</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/wortel.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Jus Wortel</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/tomat.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Jus Tomat</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/jerukpanas.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Jeruk panas</h4>
                                                    <h4 class="text-primary">Rp.10.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/jerukdingin.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Jeruk Dingin</h4>
                                                    <h4 class="text-primary">Rp.10.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/tehmanis.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Teh Manis Panas</h4>
                                                    <h4 class="text-primary">Rp.5.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/tehmanis.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Teh Manis Dingin</h4>
                                                    <h4 class="text-primary">Rp.5.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/coffekspresso.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Coffe Ekspresso</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/cappucinoice.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Cappucino Ice</h4>
                                                    <h4 class="text-primary">Rp.19.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/cappucinohot.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Cappucino Hot</h4>
                                                    <h4 class="text-primary">Rp.18.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/coffelatteice.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Cofe Latte Ice</h4>
                                                    <h4 class="text-primary">Rp.18.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/coffelatehot.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Cofe Latte Hot</h4>
                                                    <h4 class="text-primary">Rp.17.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/coffesusugularen.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Cofe Susu Gula Aren</h4>
                                                    <h4 class="text-primary">Rp.19.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/bestlatteice.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Best Latte Ice</h4>
                                                    <h4 class="text-primary">Rp.18.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/bestlattehot.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Best Latte Hot</h4>
                                                    <h4 class="text-primary">Rp.17.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
    
                                    <h1>*(Huzelunut,Tiramisu,Caramel)</h1>
    
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/macthaice.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Matcha Ice</h4>
                                                    <h4 class="text-primary">Rp.18.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/matchahot.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Matcha Hot</h4>
                                                    <h4 class="text-primary">Rp.17.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/coklatice.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Coklat Ice</h4>
                                                    <h4 class="text-primary">Rp.18.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/coklathot.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Coklat Hot</h4>
                                                    <h4 class="text-primary">Rp.17.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/redvlvt.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Red Valvet Ice</h4>
                                                    <h4 class="text-primary">Rp.18.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/redvlvt2.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Red Valvet Hot</h4>
                                                    <h4 class="text-primary">Rp.17.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/vakalpeach.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Vakal Peach</h4>
                                                    <h4 class="text-primary">Rp.25.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/beautypeach.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Beauty Peach</h4>
                                                    <h4 class="text-primary">Rp.25.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/tehtubruk.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Teh Tubruk</h4>
                                                    <h4 class="text-primary">Rp.10.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/minuman/tehtubruk2.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Teh Tubruk Susu</h4>
                                                    <h4 class="text-primary">Rp.11.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    

                            <!-- MENU PAKET-->
    
                            
                            <div id="tab-9" class="tab-pane fade show p-0">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/menu-paket/paket-prasmanan.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>PAKET GEMILANG A</h4>
                                                    <h4 class="text-primary">Rp.29.000</h4>
                                                </div>
                                                <p class="mb-0">.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/menu-paket/paket-prasmanan.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>PAKET GEMILANG B</h4>
                                                    <h4 class="text-primary">Rp.29.000</h4>
                                                </div>
                                                <p class="mb-0">.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Cooming Soon</h4>
                                                    <h4 class="text-primary"></h4>
                                                </div>
                                                <p class="mb-0">.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Cooming Soon</h4>
                                                    <h4 class="text-primary"></h4>
                                                </div>
                                                <p class="mb-0">.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Cooming Soon</h4>
                                                    <h4 class="text-primary"></h4>
                                                </div>
                                                <p class="mb-0">.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Cooming Soon</h4>
                                                    <h4 class="text-primary"></h4>
                                                </div>
                                                <p class="mb-0">.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Cooming Soon</h4>
                                                    <h4 class="text-primary"></h4>
                                                </div>
                                                <p class="mb-0">.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Cooming Soon</h4>
                                                    <h4 class="text-primary"></h4>
                                                </div>
                                                <p class="mb-0">.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <!-- MENU ANEKA SNACK -->
    
                            <div id="tab-10" class="tab-pane fade show p-0">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/aneka-snack/KongkouSnack.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Kongkou Snack</h4>
                                                    <h4 class="text-primary">Rp.20.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/aneka-snack/sosisgoreng.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Frans Freas</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/aneka-snack/rotibakar.png" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Roti Bakar Toping Meses Keju</h4>
                                                    <h4 class="text-primary">Rp.15.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/aneka-snack/rotibakarkejucoklat.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Roti Bakar Coklat Keju</h4>
                                                    <h4 class="text-primary">Rp.10.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/aneka-snack/sosisgoreng.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Sosis Goreng</h4>
                                                    <h4 class="text-primary">Rp.10.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/aneka-snack/nugget.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Nugget</h4>
                                                    <h4 class="text-primary">Rp.10.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/aneka-snack/pisangbakar.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Pisang Bakar</h4>
                                                    <h4 class="text-primary">Rp.10.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/aneka-snack/keongracun.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Keong Racun</h4>
                                                    <h4 class="text-primary">Rp.10.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="menu-item d-flex align-items-center">
                                            <img class="flex-shrink-0 img-fluid rounded-circle" src="assets/img/aneka-snack/tahutepung.jpg" alt="">
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <div class="d-flex justify-content-between border-bottom border-primary pb-2 mb-2">
                                                    <h4>Tahu Tepung Spicy</h4>
                                                    <h4 class="text-primary">Rp.10.000</h4>
                                                </div>
                                                <p class="mb-0">Consectetur adipiscing elit sed dwso eiusmod tempor incididunt ut labore.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Menu End -->

