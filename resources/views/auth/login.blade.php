<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Gemilang Cafe & Saung</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('templates/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('templates/dist/css/adminlte.min.css') }}">

  <style>
    /* Menambahkan shadow pada box login */
    .login-box {
      width: 400px;
      box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2); /* Efek shadow */
      border-radius: 10px; /* Membuat sudut lebih halus */
      background: #ffffff;
      padding: 20px;
    }

    /* Mengubah warna border atas card */
    .card-outline.card-primary {
      border-top: 4px solid #c6a475; /* Warna coklat emas */
      border-radius: 10px; /* Membuat sudut lebih halus */
    }

    /* Mengubah warna background atas card */
    .card-header {
      background-color: #c6a475 !important;
      color: white; /* Warna teks putih agar kontras */
      border-radius: 10px 10px 0 0; /* Melengkungkan hanya bagian atas */
    }

    .brand-text {
      font-size: 28px;
      font-weight: bold;
    }

    .form-control {
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      padding-left: 15px;
    }

    .input-group-text {
      border-radius: 10px;
    }

    .btn-primary {
      background-color: #c6a475;
      border: none;
      font-weight: bold;
      transition: 0.3s;
    }

    .btn-primary:hover {
      background-color: #b08950;
    }

    .icheck-primary input:checked ~ label {
      color: #c6a475;
    }

    /* Animasi hover pada form */
    .login-box:hover {
      transform: translateY(-5px);
      transition: 0.3s;
    }
  </style>
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="card card-outline card-primary text-center">
      <div class="card-header">
          <div style="margin-bottom: 10px;">
            <img src="{{ asset('assets/icon-gemilang.png') }}" alt="Gemilang Icon" style="width: 70px; height: 70px;">
          </div>
          <div class="brand-text">
            <b>Gemilang</b><br>
            Cafe & Saung
          </div>
      </div>
    <div class="card-body">
      <p class="login-box-msg">Silahkan masuk terlebih dahulu</p>

      <form action="{{ route('login_proses') }}" method="post">
        @csrf
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        @error('email')
        <small>{{ $message }}</small>
        @enderror

        <div class="input-group mb-3">
          <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-eye" id="toggle-password" style="cursor: pointer;"></span>
            </div>
          </div>
        </div>
        @error('password')
        <small>{{ $message }}</small>
        @enderror

        <div class="row">
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-12">
            <div class="icheck-primary" style="text-align: left;">
              <input type="checkbox" id="remember">
              <label for="remember">
                Simpan
              </label>
            </div>
          </div>
        </div>
      </form>
      <p class="mb-1">
        <a href="{{ route('forgot-password')  }}">Lupa Password ?</a>
      </p>
      <p class="mb-0">
        <a href="{{ route('register') }}" class="text-center">Buat Akun Baru</a>
      </p>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="{{ asset('templates/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('templates/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('templates/dist/js/adminlte.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if ($message = Session::get('success'))
    <script>
        Swal.fire('{{ $message }}');
    </script>
@endif

@if($message = Session::get('failed'))
    <script>
      Swal.fire('{{ $message }}');
    </script>
@endif

<script>
  // Script untuk toggle visibility password
  const togglePassword = document.getElementById('toggle-password');
  const passwordField = document.getElementById('password');

  togglePassword.addEventListener('click', function () {
    if (passwordField.type === "password") {
      passwordField.type = "text";
      togglePassword.classList.remove("fas", "fa-eye");
      togglePassword.classList.add("fas", "fa-eye-slash");
    } else {
      passwordField.type = "password";
      togglePassword.classList.remove("fas", "fa-eye-slash");
      togglePassword.classList.add("fas", "fa-eye");
    }
  });
</script>

</body>
</html>
