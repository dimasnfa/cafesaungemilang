@extends('landing.components.layout')

@section('title', 'Home - Gemilang Cafe')

@section('content')

      <!-- Hero Start -->
      <div class="container-fluid py-6 my-6 mt-0" style="background-image: url('assets/img/banner/icon-about.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container text-center animated bounceInDown" style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px;">
            <h1 class="display-1 mb-4">About Us</h1>
            <ol class="breadcrumb justify-content-center mb-0 animated bounceInDown">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                <li class="breadcrumb-item text-dark" aria-current="page">About</li>
            </ol>
        </div>
    </div>
    <!-- Hero End -->

    <!-- About Content -->
    <div class="container-fluid py-6">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <img src="{{ asset('assets/img/banner/about.jpg') }}" class="img-fluid rounded" alt="About Gemilang Cafe">
                </div>
                <div class="col-lg-7">
                    <small class="d-inline-block fw-bold text-dark text-uppercase bg-light border border-primary rounded-pill px-4 py-1 mb-3">About Us</small>
                    <p class="mb-4">Gemilang Cafe & Saung di Desa Sindang menghadirkan suasana pedesaan asri dengan cita rasa kuliner khas. Kami menawarkan makanan dan minuman berkualitas dengan harga terjangkau. Dengan sistem pesanan online, Anda bisa memilih tempat duduk di lantai 1 atau 2 sesuai keinginan Anda.</p>
                    <div class="row g-4 text-dark mb-5">
                        <div class="col-sm-6">
                            <i class="fas fa-share text-primary me-2"></i>Menu Lezat & Beragam
                        </div>
                        <div class="col-sm-6">
                            <i class="fas fa-share text-primary me-2"></i>Layanan Pelanggan 24/7
                        </div>
                        <div class="col-sm-6">
                            <i class="fas fa-share text-primary me-2"></i>Pesanan Fleksibel & Mudah
                        </div>
                        <div class="col-sm-6">
                            <i class="fas fa-share text-primary me-2"></i>Tempat Luas & Nyaman
                        </div>
                    </div>
                    <a href="{{ url('/about') }}" class="btn btn-primary py-3 px-5 rounded-pill">About Us<i class="fas fa-arrow-right ps-2"></i></a>
                </div>
            </div>
        </div>
    </div>

      <!-- Fact Start-->
      <div class="container-fluid faqt py-6 d-flex justify-content-center">
        <div class="container">
            <div class="row g-4 align-items-center justify-content-center">
                <div class="col-lg-7">
                    <div class="row g-4 justify-content-center">
                        <div class="col-sm-4 wow bounceInUp" data-wow-delay="0.3s">
                            <div class="faqt-item bg-primary rounded p-4 text-center">
                                <i class="fas fa-users fa-4x mb-4 text-white"></i>
                                <h1 class="display-4 fw-bold" data-toggle="counter-up">689</h1>
                                <p class="text-dark text-uppercase fw-bold mb-0">Kepuasan Pelanggan adalah Prioritas</p>
                            </div>
                        </div>
                        <div class="col-sm-4 wow bounceInUp" data-wow-delay="0.5s">
                            <div class="faqt-item bg-primary rounded p-4 text-center">
                                <i class="fas fa-users-cog fa-4x mb-4 text-white"></i>
                                <h1 class="display-4 fw-bold" data-toggle="counter-up">107</h1>
                                <p class="text-dark text-uppercase fw-bold mb-0">Chef Profesional dan Berpengalaman
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-4 wow bounceInUp" data-wow-delay="0.7s">
                            <div class="faqt-item bg-primary rounded p-4 text-center">
                                <i class="fas fa-coffee fa-4x mb-4 text-white"></i>
                                <h1 class="display-4 fw-bold" data-toggle="counter-up">253</h1>
                                <p class="text-dark text-uppercase fw-bold mb-0">Tempat Nyaman untuk Bersantai</p>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
