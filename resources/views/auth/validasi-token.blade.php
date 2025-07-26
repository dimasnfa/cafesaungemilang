<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - Gemilang Cafe & Saung</title>

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
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary text-center">
            <div class="card-body">
                <p class="login-box-msg">Masukkan password baru!</p>
                <form action="{{ route('reset-password-act') }}" method="POST">

                    @csrf
                    <input type="hidden" name="token" value="{{$token}}">
                    <div class="input-group mb-3">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan Password Baru" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></span>
                            </div>
                        </div>
                    </div>
                    @error('password')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror

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
        <script>Swal.fire('{{ session('success') }}');</script>
    @endif

    @if (session('failed'))
        <script>Swal.fire('{{ session('failed') }}');</script>
    @endif

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            let passwordField = document.getElementById('password');
            let icon = this;
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>
