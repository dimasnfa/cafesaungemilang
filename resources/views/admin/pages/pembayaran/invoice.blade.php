<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $pembayaran->order_id }}</title>
    <style>
        :root {
            --gold: #F2C27F;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #fffaf2;
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            width: 700px;
            margin: 30px auto;
            padding: 20px;
            border: 3px solid var(--gold);
            background-color: #fff;
        }

        .header {
            text-align: center;
            background-color: var(--gold);
            padding: 15px;
            border-radius: 8px;
            background-image: url('{{ asset("assets/icon-header.png") }}');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            position: relative;
            color: #000;
        }

        .header .overlay {
            background-color: rgba(255, 255, 255, 0.85);
            display: inline-block;
            padding: 10px 20px;
            border-radius: 10px;
        }

        .header img {
            width: 80px;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
        }

        h3.text-center {
            text-align: center;
            color: #333;
            margin-top: 20px;
            border-bottom: 2px dashed var(--gold);
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th {
            background-color: var(--gold);
            color: #000;
            padding: 8px;
        }

        td {
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }

        .text-left { text-align: left; }
        .text-right { text-align: right; }

        .info-list {
            list-style: none;
            padding: 0;
        }

        .info-list li {
            margin-bottom: 5px;
        }

        .bg-gold {
            background-color: var(--gold);
            padding: 10px;
        }

        .mt-3 { margin-top: 1rem; }

        .footer {
            text-align: center;
            background-color: var(--gold);
            padding: 15px;
            border-radius: 8px;
        }

        .footer img {
            margin-top: 10px;
        }

        .footer h3 {
            margin-bottom: 5px;
            color: #000;
        }

        .footer p {
            margin: 0;
            font-style: italic;
        }

        .contact {
            margin-top: 20px;
            font-size: 13px;
        }

        .contact strong {
            color: #000;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .header {
                background-image: url('{{ asset("assets/icon-header.png") }}') !important;
            }

            /* Hide default browser print header/footer */
            html, body {
                height: auto !important;
                overflow: hidden;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-container">
        {{-- HEADER --}}
        <div class="header">
            <div class="overlay">
                <img src="{{ asset('assets/icon-gemilang.png') }}" alt="Logo">
                <h2>GEMILANG <br> CAFE & SAUNG</h2>
            </div>
        </div>

        <h3 class="text-center">- INVOICE -</h3>

        {{-- INFORMASI UMUM --}}
        <ul class="info-list">
            <li><strong>Nomor Invoice:</strong> {{ $pembayaran->order_id }}</li>
            <li><strong>Tanggal Pesanan:</strong> {{ \Carbon\Carbon::parse($pembayaran->tanggal_pesanan)->format('d-m-Y') }}</li>
            <li><strong>Waktu Pesanan:</strong> {{ \Carbon\Carbon::parse($pembayaran->waktu_pesanan)->format('H:i:s') }}</li>
            @if($pembayaran->jenis_pesanan === 'dinein')
                <li><strong>Nomor Meja:</strong> {{ $pembayaran->nomor_meja ?? '-' }}</li>
            @endif
            <li><strong>Jenis Pesanan:</strong> {{ ucfirst($pembayaran->jenis_pesanan) }}</li>
            <li><strong>Metode Pembayaran:</strong> {{ ucfirst($pembayaran->metode_pembayaran) }}</li>
        </ul>

        {{-- TABEL MENU --}}
        <table>
            <thead>
                <tr>
                    <th>KODE MENU</th>
                    <th>MENU</th>
                    <th>QTY</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalHarga = 0; @endphp
                @foreach ($pembayaran->pesanan->menu as $menu)
                    @php
                        $jumlah = $menu->pivot->jumlah ?? 0;
                        $harga = $menu->harga ?? 0;
                        $subtotal = $jumlah * $harga;
                        $totalHarga += $subtotal;
                    @endphp
                    <tr>
                        <td>{{ $menu->id }}</td>
                        <td class="text-left">{{ $menu->nama_menu }}</td>
                        <td>{{ $jumlah }}</td>
                        <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                @for ($i = $pembayaran->pesanan->menu->count(); $i < 6; $i++)
                    <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
                @endfor

                <tr>
                    <td colspan="3" class="text-right"><strong>Total Harga</strong></td>
                    <td class="bg-gold"><strong>Rp {{ number_format($totalHarga, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        {{-- DETAIL PEMBAYARAN --}}
        <div class="mt-3">
            <strong>Detail Pembayaran:</strong><br>
            <ul class="info-list">
                <li><strong>Metode Pembayaran:</strong> {{ ucfirst($pembayaran->metode_pembayaran) }}</li>
                <li><strong>Nama {{ $pembayaran->jenis_pesanan === 'takeaway' ? 'Pelanggan' : 'Rekening' }}:</strong> {{ $pembayaran->nama_pelanggan }}</li>
                @if($pembayaran->jenis_pesanan === 'takeaway')
                    <li><strong>Nomor Whatsapp:</strong> {{ $pembayaran->nomor_wa }}</li>
                @else
                    <li><strong>Detail Bank:</strong> {{ $pembayaran->metode_pembayaran === 'qris' ? 'Mandiri' : '-' }}</li>
                @endif
            </ul>
        </div>

        {{-- FOOTER --}}
        <div class="footer mt-3">
            <img src="{{ asset('assets/cap.png') }}" alt="Cap" width="100">
            <h3>GEMILANG <br> CAFE & SAUNG</h3>
            <p>Terimakasih atas kunjungan & pesanan anda</p>
        </div>

        {{-- KONTAK --}}
        <div class="contact text-left">
            <strong>Follow kami:</strong><br>
            📱 IG: Cafe & Saung Gemilang<br>
            📘 FB: Cafe & Saung Gemilang<br><br>
            <strong>Alamat:</strong><br>
            Jln. Raya Sindang, Pagerwetan, Kec. Adiwerna, Tegal, Jawa Tengah, 52451<br>
            No Telepon : 082324437584
        </div>
    </div>
</body>
</html>
