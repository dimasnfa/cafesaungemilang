@extends('landing.components.layout')

@section('title', 'Home - Gemilang Cafe')

@section('content')

    <!-- Hero Start -->
    <div class="container-fluid py-6 my-6 mt-0" style="background-image: url('assets/img/banner/icon-about.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container text-center animated bounceInDown" style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px;">
            <h1 class="display-1 mb-4">Service</h1>
            <ol class="breadcrumb justify-content-center mb-0 animated bounceInDown">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                <li class="breadcrumb-item text-dark" aria-current="page">Services</li>
            </ol>
        </div>
    </div>
    <!-- Hero End -->

      <!-- Service Start -->
      <div class="container-fluid service py-6">
        <div class="container">
            <div class="text-center wow bounceInUp" data-wow-delay="0.1s">
                <small class="d-inline-block fw-bold text-dark text-uppercase bg-light border border-primary rounded-pill px-4 py-1 mb-3">Service</small>
                <h1 class="display-5 mb-5">What We Offer</h1>
            </div>
            
            <div class="row g-4">
                <!-- Service Item 1 -->
                <div class="col-lg-3 col-md-6 col-sm-12 wow bounceInUp" data-wow-delay="0.1s">
                    <div class="bg-light rounded service-item">
                        <div class="service-content d-flex align-items-center justify-content-center p-4">
                            <div class="service-content-icon text-center">
                                <i class="fas fa-parking fa-7x text-primary mb-4"></i>
                                <h4 class="mb-3">Area Parkir Luas</h4>
                                <p class="mb-4">Nikmati area parkir yang luas dan aman untuk kendaraan Anda.</p>
                                <a href="#" class="btn btn-primary px-4 py-2 rounded-pill">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Service Item 2 -->
                <div class="col-lg-3 col-md-6 col-sm-12 wow bounceInUp" data-wow-delay="0.3s">
                    <div class="bg-light rounded service-item">
                        <div class="service-content d-flex align-items-center justify-content-center p-4">
                            <div class="service-content-icon text-center">
                                <i class="fas fa-store fa-7x text-primary mb-4"></i>
                                <h4 class="mb-3">Tempat Luas & Nyaman</h4>
                                <p class="mb-4">Tempat luas yang nyaman untuk berbagai acara dan pertemuan bersama keluarga atau teman.</p>
                                <a href="#" class="btn btn-primary px-4 py-2 rounded-pill">Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Service Item 3 -->
                <div class="col-lg-3 col-md-6 col-sm-12 wow bounceInUp" data-wow-delay="0.5s">
                    <div class="bg-light rounded service-item">
                        <div class="service-content d-flex align-items-center justify-content-center p-4">
                            <div class="service-content-icon text-center">
                                <i class="fas fa-tree fa-7x text-primary mb-4"></i>
                                <h4 class="mb-3">View Pedesaan</h4>
                                <p class="mb-4">Selain tempat yang bersih, kami juga menjanjikan view pedesaan yang cantik.</p>
                                <a href="#" class="btn btn-primary px-4 py-2 rounded-pill">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Service Item 4 -->
                <div class="col-lg-3 col-md-6 col-sm-12 wow bounceInUp" data-wow-delay="0.7s">
                    <div class="bg-light rounded service-item">
                        <div class="service-content d-flex align-items-center justify-content-center p-4">
                            <div class="service-content-icon text-center">
                                <i class="fas fa-utensils fa-7x text-primary mb-4"></i>
                                <h4 class="mb-3">Aneka Menu Makanan</h4>
                                <p class="mb-4">Lezat dan Mengenyangkan Setiap Saat Cita Rasa yang Menggugah Selera</p>
                                <a href="#" class="btn btn-primary px-4 py-2 rounded-pill">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
      </div>
            </div>
        </div>
    </div>
    <!-- Service End -->

@endsection
