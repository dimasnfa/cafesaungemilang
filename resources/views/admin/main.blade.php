<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GEMILANG CAFE & SAUNG</title>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('templates/dist/css/adminlte.min.css') }}">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    html, body {
        height: 100%;
        margin: 0;
    }

    .wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .content-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .content {
        flex: 1;
        padding: 1rem;
        overflow-x: auto; /* ✅ scroll horizontal jika tabel besar */
        width: 100%;
    }

    .main-footer {
        background: #f8f9fa;
        padding: 10px 20px;
        text-align: center;
        width: 100%;
    }
  </style>

  @stack('styles')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  {{-- Navbar --}}
  @include('admin.components.navbar')

  {{-- Sidebar --}}
  @include('admin.components.sidebar')

  {{-- Content Wrapper --}}
  <div class="content-wrapper">
    {{-- Content Header --}}
    <section class="content-header">
      <div class="container-fluid">
        @yield('header')
      </div>
    </section>

    {{-- Main Content --}}
    <div class="content"> {{-- ✅ Ganti dari <section class="content"> ke <div class="content"> --}}
      @yield('content')
    </div>
  </div>

  {{-- Footer --}}
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0
    </div>
    <strong>Copyright &copy; 2025 <a href="https://www.instagram.com/_gemilangtegal">Gemilang</a>.</strong> All rights reserved.
  </footer>
</div>

{{-- Scripts --}}
<script src="{{ asset('/templates/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('/templates/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/templates/dist/js/adminlte.min.js') }}"></script>
@stack('scripts')
</body>
</html>
