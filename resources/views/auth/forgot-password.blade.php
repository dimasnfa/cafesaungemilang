<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - Gemilang Cafe & Saung</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
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

        .form-control {
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #c6a475;
            border: none;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #b08950;
        }

        .back-to-login {
            margin-top: 15px;
            font-size: 16px;
            color: #c6a475;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-to-login:hover {
            text-decoration: underline;
        }

        .brand-text {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary text-center">
            <div class="card-header">
                <img src="{{ asset('assets/icon-gemilang.png') }}" alt="Gemilang Icon" style="width: 70px;">
                <div class="brand-text"><b>Gemilang</b><br>Cafe & Saung</div>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Masukkan email Anda untuk mereset password</p>

                <form action="{{ route('forgot-password-act') }}" method="POST">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="Email" value="{{ old('email') }}" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <div class="invalid-feedback text-left w-100">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary btn-block">Kirim</button>
                        </div>
                    </div>
                </form>

                <a href="{{ route('login') }}" class="back-to-login">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </div>
    </div>

    <script src="{{ asset('templates/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('templates/dist/js/adminlte.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}'
            });
        </script>
    @endif

    @if (session('failed'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('failed') }}'
            });
        </script>
    @endif
</body>
</html>
