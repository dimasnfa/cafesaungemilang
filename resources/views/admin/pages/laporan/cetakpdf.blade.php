<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <style>
        :root {
            --gold: #F2C27F;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #fff;
        }

        /* Container tetap di tengah dan rapi untuk A4 */
        .laporan-container {
            width: 700px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid var(--gold);
            background-color: #fff;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            background-color: var(--gold);
            padding: 20px;
            border-radius: 8px;
            background-image: url('{{ public_path("assets/icon-header.png") }}');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            color: #000;
            position: relative;
        }

        .header .overlay {
            background-color: rgba(255, 255, 255, 0.85);
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-block;
        }

        .header img {
            width: 75px;
        }

        .header h2 {
            margin: 5px 0 0;
            font-size: 22px;
        }

        h3.text-center {
            text-align: center;
            color: #333;
            margin-top: 20px;
            border-bottom: 2px dashed var(--gold);
            padding-bottom: 10px;
        }

        .info-table {
            width: 100%;
            margin-top: 10px;
            font-size: 13px;
        }

        .info-table td {
            padding: 6px 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        thead {
            background-color: var(--gold);
        }

        table, th, td {
            border: 1px solid #000;
        }

        th {
            padding: 12px 8px 14px; /* Tambahkan padding bawah */
            font-size: 13px;
            text-align: center;
        }

        td {
            padding: 8px;
            font-size: 13px;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bg-gold {
            background-color: var(--gold);
            font-weight: bold;
        }

        .footer {
            background-color: var(--gold);
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            border-radius: 6px;
        }

        .footer p {
            margin: 4px 0;
            font-style: italic;
        }

        .contact {
            font-size: 12px;
            margin-top: 10px;
            text-align: left;
            line-height: 1.5;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                background: none;
            }

            .laporan-container {
                margin: auto;
            }
        }
    </style>
</head>
<body>
    <div class="laporan-container">
        {{-- HEADER --}}
        <div class="header">
            <div class="overlay">
                <img src="{{ public_path('assets/icon-gemilang.png') }}" alt="Logo">
                <h2>GEMILANG <br> CAFE & SAUNG</h2>
            </div>
        </div>

        <h3 class="text-center">- LAPORAN TRANSAKSI -</h3>

        {{-- INFO --}}
        <table class="info-table">
            <tr>
                <td><strong>Tanggal Cetak Laporan</strong></td>
                <td>: {{ $tanggalCetak }}</td>
            </tr>
            <tr>
                <td><strong>Cetak Laporan</strong></td>
                <td>: 
                    @if ($filterType === 'harian') Harian 
                    @elseif ($filterType === 'bulanan') Bulanan 
                    @else Tahunan 
                    @endif
                </td>
            </tr>
        </table>

        {{-- TABEL --}}
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Menu</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporans as $laporan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($laporan->pesanan->tanggal_pesanan)->format('d-m-Y') }}</td>
                        <td>{{ ucfirst($laporan->pesanan->jenis_pesanan) }}</td>
                        <td>{{ $laporan->menu->nama_menu }}</td>
                        <td>{{ $laporan->menu->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $laporan->jumlah }}</td>
                        <td>Rp {{ number_format($laporan->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">Tidak ada data transaksi</td></tr>
                @endforelse

                @if(count($laporans))
                <tr>
                    <td colspan="6" class="text-right"><strong>Total Pendapatan</strong></td>
                    <td class="bg-gold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        {{-- FOOTER --}}
        <div class="footer">
            <p>Terima kasih telah menggunakan sistem kami</p>
            <p><strong>GEMILANG CAFE & SAUNG</strong></p>
        </div>

   {{-- KONTAK
        <div class="contact text-left">
            <strong>Follow kami:</strong><br>
            📱 IG: Cafe & Saung Gemilang<br>
            📘 FB: Cafe & Saung Gemilang<br><br>
            <strong>Alamat:</strong><br>
            Jln. Raya Sindang, Pagerwetan, Kec. Adiwerna, Tegal, Jawa Tengah, 52451<br>
            No Telepon : 082324437584
        </div>
    </div> --}}
</body>
</html>
