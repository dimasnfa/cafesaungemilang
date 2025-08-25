<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <title>@yield('title') | Gemilang Cafe & Saung</title> --}}

    <!-- Import TailwindCSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Import Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/rekomendasi.css') }}">

    <!-- Import rekomendasi.css -->
    <link href="{{ asset('css/rekomendasi.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #C6A475; /* Sesuai tema rekomendasi */
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen leading-normal tracking-normal">

        <br>
        <div class="container mx-auto flex justify-between items-center">
            {{-- <a href="/" class="text-xl font-bold text-green-700">Gemilang Cafe & Saung</a> --}}
            <div>
                {{-- Navbar links (aktifkan kalau mau) --}}
                {{-- 
                <a href="{{ url('/cart') }}" class="text-gray-600 hover:text-green-500 mx-2">Keranjang</a>
                <a href="{{ url('/menu') }}" class="text-gray-600 hover:text-green-500 mx-2">Menu</a>
                <a href="{{ url('/') }}" class="text-gray-600 hover:text-green-500 mx-2">Beranda</a>
                --}}
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-inner p-4 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} Gemilang Cafe & Saung. All rights reserved.
    </footer>

</body>
</html>
