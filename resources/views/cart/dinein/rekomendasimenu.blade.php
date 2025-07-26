@extends('cart.layout.rekomendasi')

@section('title', 'Rekomendasi Menu Terpopuler - Dine-In')

@section('content')
<div class="recommendation-section px-4">
    <div class="text-center mb-2">
        <h2 class="text-2xl md:text-3xl font-semibold text-[#c6a475] inline-block border-b border-black pb-2">
            Gemilang Cafe & Saung
        </h2>
<div class="recommendation-section px-4">
    <div class="text-center mb-6">
        <h2 class="text-3xl font-semibold text-[#c6a475] border-b border-black inline-block pb-2">
            Rekomendasi Menu Terpopuler
        </h2>
        <p class="text-white mt-2">Menu yang paling sering dipesan oleh pelanggan Dine-In</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse ($menus as $menu)
            @php
                $kategori = $menu->kategori->nama_kategori ?? 'lainnya';
                $folder = strtolower(str_replace([' ', '&'], '-', $kategori));
                $defaultGambar = 'default.png';

                $customImages = [
                    'Makanan' => [
                        'Nasi' => 'nasi.png',
                        'Ayam Goreng' => 'ayamgoreng.jpg',
                        'Ayam Bakar' => 'ayambakar.jpg',
                        'Ayam Mentega' => 'ayamentega.jpg',
                        'Ayam Lada Hitam' => 'ayamladahitam.jpg',
                        'Ayam Lombok Ijo' => 'ayamlombokijo.jpg',
                        'Ayam Asam Manis' => 'ayamasammanis.jpg',
                        'Ayam Saos Padang' => 'ayamsaospadang.jpg',
                        'Ayam Rica-Rica' => 'ayamricarica.jpg',
                        'Sop Ayam' => 'sopayam.jpg',
                        'Garang Asem Ayam' => 'garangasem.jpg',
                        'Sambal Mentah' => 'sambalmentah.jpg',
                        'Sambal Pecak' => 'sambalpecak.jpg',
                        'Sambal Terasi' => 'sambalterasi.jpg',
                        'Sambal Geprek' => 'sambalgeprek.jpg',
                        'Sambal Bawang' => 'sambalbawang.jpg',
                        'Sambal Ijo' => 'sambalijo.jpg',
                        'Sambal Dabu-Dabu' => 'sambaldabu.jpg',
                    ],
                    'Minuman' => [
                        'Jus Alpukat' => 'alpukat.jpg',
                        'Jus Apel' => 'apel.jpg',
                        'Jus Strawberry' => 'strawberry.jpg',
                        'Jus Jeruk' => 'jeruk.jpg',
                        'Jus Tomat' => 'tomat.jpg',
                        'Jus Mangga' => 'mangga.jpg',
                        'Jus Melon' => 'melon.jpg',
                        'Jus Fibar' => 'fibar.jpg',
                        'Jus Wortel' => 'wortel.jpg',
                        'Jeruk Panas' => 'jerukpanas.jpg',
                        'Jeruk Dingin' => 'jerukdingin.jpg',
                        'Teh Manis Panas' => 'tehmanis.jpg',
                        'Teh Manis Dingin' => 'tehmanis.jpg',
                        'Coffe Ekspresso' => 'coffekspresso.jpg',
                        'Cappucino Ice' => 'cappucinoice.jpg',
                        'Cappucino Hot' => 'cappucinohot.jpg',
                        'Cofe Susu Gula Aren' => 'coffesusugularen.jpg',
                        'Best Latte Ice' => 'bestlattehot.jpg',
                        'Cofe Latte Ice' => 'coffelatteice.jpg',
                        'Cofe Latte Hot' => 'coffelatehot.jpg',
                        'Matcha Ice' => 'macthaice.jpg',
                        'Matcha Hot' => 'matchahot.jpg',
                        'Coklat Ice' => 'coklatice.jpg',
                        'Coklat Hot' => 'coklathot.jpg',
                        'Red Valvet Ice' => 'redvlvt.jpg',
                        'Red Valvet Hot' => 'redvlvt.jpg',
                        'Vakal Peach' => 'vakalpeach.jpg',
                        'Beauty Peach' => 'beautypeach.jpg',
                        'Teh Tubruk' => 'tehtubruk.jpg',
                        'Teh Tubruk Susu' => 'tehtubruk2.jpg',
                    ],
                    'Nasi dan Mie' => [
                        'Mie Goreng' => 'miegoreng.png',
                        'Indomie Rebus' => 'indomierebus.png',
                        'Indomie Goreng+toping' => 'indomiegoreng.png',
                        'Nasi Goreng Gemilang' => 'nasigorenggemilang.png',
                        'Nasi Goreng Seafood' => 'nasigorengseafood.png',
                        'Nasi Goreng Ayam' => 'nasigorengayam.png',
                        'Kwetiau Goreng' => 'kwetiau.png',
                        'Kwetiau Rebus' => 'kwetiaurebus.png',
                    ]
                ];

                $gambar = $menu->gambar ?? ($customImages[$kategori][$menu->nama_menu] ?? $defaultGambar);
            @endphp

            <div class="menu-card">
                <img src="{{ asset('assets/img/' . $folder . '/' . $gambar) }}" 
                     alt="{{ $menu->nama_menu }}" 
                     onerror="this.onerror=null;this.src='{{ asset('assets/img/default.png') }}';">

                <div class="menu-card-content">
                    <h3>{{ $menu->nama_menu }}</h3>
                    <p>Sudah dipesan: {{ $menu->detailpesanan_sum_jumlah ?? 0 }}</p>
                    <a href="{{ url('dinein/booking/' . $meja_id . '?from_qr=yes') }}">
                        Pesan Sekarang
                    </a>
                </div>
            </div>
        @empty
            <div class="text-white text-center col-span-4">
                Belum ada data menu populer yang tersedia.
            </div>
        @endforelse
    </div>
</div>
@endsection
