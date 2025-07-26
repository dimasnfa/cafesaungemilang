<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Profile CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="profile-container text-center">
        <h4 class="mb-3">Profil Saya</h4>
        <img src="{{ asset('assets/img/user-gemilang.png') }}" alt="User Avatar" class="rounded-circle" width="100" height="100">
        
        <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
        </div>
        
        <div class="form-group">
            <label>Kata Sandi</label>
            <input type="password" id="passwordField" class="form-control" value="********" readonly>
            <small class="text-muted d-block mt-1">Kata sandi tidak dapat ditampilkan.</small>
        </div>
        
        <br>
        <br>
        <small class="text-muted d-block mt-1">*Jika anda lupa sandi,silahkan ubah sandi anda</small>
        <a href="{{ route('forgot-password') }}" class="btn btn-outline-primary w-100 mt-3">Ubah Sandi</a>
        <button onclick="window.history.back()" class="btn btn-secondary w-100 mt-2">Kembali</button>
    </div>

    <script>
        document.getElementById("togglePassword").addEventListener("click", function() {
            var passwordField = document.getElementById("passwordField");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            }
        });
    </script>
</body>
</html>
