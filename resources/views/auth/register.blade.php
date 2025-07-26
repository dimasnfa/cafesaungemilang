<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Gemilang Cafe & Saung</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('templates/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('templates/dist/css/adminlte.min.css') }}">

  <style>
    .login-box {
      width: 400px;
      box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
      border-radius: 10px;
      background: #ffffff;
      padding: 20px;
    }

    .card-outline.card-primary {
      border-top: 4px solid #c6a475;
      border-radius: 10px;
    }

    .card-header {
      background-color: #c6a475 !important;
      color: white;
      border-radius: 10px 10px 0 0;
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
        <p class="login-box-msg">Silahkan buat akun baru</p>

        <form action="{{ route('register_proses') }}" method="post">
          @csrf

          <!-- Nama Lengkap -->
          <div class="input-group mb-3">
            <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" value="{{ old('nama') }}">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          @error('nama')
            <small class="text-danger">{{ $message }}</small>
          @enderror

          <!-- Email -->
          <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          @error('email')
            <small class="text-danger">{{ $message }}</small>
          @enderror

          <!-- Password -->
          <div class="input-group mb-3">
            <input type="password" name="password" id="password" class="form-control" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-eye" id="toggle-password" style="cursor: pointer;"></span>
              </div>
            </div>
          </div>
          @error('password')
            <small class="text-danger">{{ $message }}</small>
          @enderror

          <div class="row">
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary btn-block">Buat Akun</button>
            </div>
          </div>
        </form>

        <p class="mb-1">
          <a href="{{ route('forgot-password')}} ">Lupa Password ?</a>
        </p>
      </div>
    </div>
  </div>

  <script src="{{ asset('templates/plugins/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('templates/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
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
