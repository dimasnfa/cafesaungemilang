@extends('admin.main')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="d-sm-flex">
            <div class="dropdown no-arrow mr-3">
                <a class="dropdown-toggle btn btn-primary btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Export Options:</div>
                    <a class="dropdown-item" href="{{ route('admin.laporan.export-pdf') }}">
                        <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-gray-400"></i>
                        PDF Report
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.laporan.export-csv') }}">
                        <i class="fas fa-file-csv fa-sm fa-fw mr-2 text-gray-400"></i>
                        CSV Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Statistik Cards -->
    <div class="row">
        <!-- Total Pesanan Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pesanan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPesanan }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pesanan Hari Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pesanan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pesananHariIni }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Pendapatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Menu Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Menu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMenu }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-utensils fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Charts -->
    <div class="row">
        <!-- Diagram Bulat - Jenis Pesanan -->
        <div class="col-xl-6 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Jenis Pesanan</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Aksi:</div>
                            <a class="dropdown-item" href="#">Refresh Data</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="jenisPesananChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Dine-In
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Takeaway
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Pesanan -->
        <div class="col-xl-6 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Status Pesanan</h6>
                </div>
                <div class="card-body">
                    @foreach($statusPesanan as $status)
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                @if($status->status_pesanan == 'pending')
                                    <i class="fas fa-clock text-warning"></i>
                                @elseif($status->status_pesanan == 'dibayar')
                                    <i class="fas fa-check-circle text-success"></i>
                                @elseif($status->status_pesanan == 'selesai')
                                    <i class="fas fa-flag-checkered text-info"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </div>
                            <div>
                                <div class="font-weight-bold text-gray-800">
                                    {{ ucfirst($status->status_pesanan) }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-weight-bold text-gray-800">{{ $status->total }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Menu Populer Chart -->
    <div class="row">
        <!-- Diagram Batang - Menu Populer -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Menu Populer (Top 10)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="menuPopulerChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Pesanan Terbaru & Pendapatan Mingguan -->
    <div class="row">
        <!-- Pesanan Terbaru -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pesanan Terbaru</h6>
                    <a href="{{ route('admin.pesanan.index') }}" class="btn btn-primary btn-sm">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Jenis</th>
                                    <th>Pelanggan/Meja</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pesananTerbaru as $pesanan)
                                <tr>
                                    <td>#{{ $pesanan->id }}</td>
                                    <td>
                                        <span class="badge badge-{{ $pesanan->jenis_pesanan == 'dinein' ? 'primary' : 'success' }}">
                                            {{ ucfirst($pesanan->jenis_pesanan) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($pesanan->jenis_pesanan == 'dinein')
                                            Meja {{ $pesanan->meja->nomor_meja ?? '-' }}
                                        @else
                                            {{ $pesanan->nama_pelanggan ?? '-' }}
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if($pesanan->status_pesanan == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($pesanan->status_pesanan == 'dibayar')
                                            <span class="badge badge-success">Dibayar</span>
                                        @elseif($pesanan->status_pesanan == 'selesai')
                                            <span class="badge badge-info">Selesai</span>
                                        @else
                                            <span class="badge badge-danger">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada pesanan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendapatan Mingguan -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pendapatan 7 Hari Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="pendapatanMingguanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Data dari controller
const jenisPesananData = @json($jenisPesananData);
const menuPopulerData = @json($menuPopuler);
const pendapatanMingguanData = @json($pendapatanMingguan);

// Plugin untuk menampilkan angka di setiap segment doughnut chart
const datalabelsPlugin = {
    id: 'datalabels',
    afterDatasetsDraw: function(chart) {
        if (chart.config.type !== 'doughnut') return;
        
        var ctx = chart.ctx;
        chart.data.datasets.forEach(function(dataset, i) {
            var meta = chart.getDatasetMeta(i);
            if (!meta.hidden) {
                meta.data.forEach(function(element, index) {
                    // Draw the text in white, with bold font
                    ctx.fillStyle = '#fff';
                    ctx.font = 'bold 20px Arial';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    
                    var position = element.tooltipPosition();
                    var value = dataset.data[index];
                    
                    // Display only the value (no percentage)
                    ctx.fillText(value, position.x, position.y);
                });
            }
        });
    }
};

// Plugin untuk menampilkan total di tengah doughnut
const centerTextPlugin = {
    id: 'centerText',
    beforeDraw: function(chart) {
        if (chart.config.type !== 'doughnut') return;
        if (chart.config.options.elements && chart.config.options.elements.center) {
            var ctx = chart.ctx;
            var centerConfig = chart.config.options.elements.center;
            var fontStyle = centerConfig.fontStyle || 'Arial';
            var txt = centerConfig.text;
            var color = centerConfig.color || '#000';

            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
            var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
            
            // Split text by newline
            var lines = txt.split('\n');
            var lineHeight = centerConfig.lineHeight || 25;
            var fontSize = centerConfig.fontSize || 20;
            
            // Adjust center Y for multiple lines
            var totalHeight = lines.length * lineHeight;
            centerY = centerY - (totalHeight / 2) + (lineHeight / 2);
            
            lines.forEach(function(line, index) {
                var currentFontSize = (index === 0) ? fontSize - 2 : fontSize + 2;
                var fontWeight = (index === 0) ? 'normal' : 'bold';
                
                ctx.font = fontWeight + ' ' + currentFontSize + 'px ' + fontStyle;
                ctx.fillStyle = color;
                ctx.fillText(line, centerX, centerY + (index * lineHeight));
            });
        }
    }
};

// Register plugins globally
Chart.register(datalabelsPlugin, centerTextPlugin);

// Chart 1: Diagram Bulat - Jenis Pesanan
const ctxJenis = document.getElementById('jenisPesananChart').getContext('2d');
const jenisPesananChart = new Chart(ctxJenis, {
    type: 'doughnut',
    data: {
        labels: jenisPesananData.map(item => 
            item.jenis_pesanan === 'dinein' ? 'Dine-In' : 'Takeaway'
        ),
        datasets: [{
            data: jenisPesananData.map(item => item.total),
            backgroundColor: ['#4e73df', '#1cc88a'],
            hoverBackgroundColor: ['#2e59d9', '#17a673'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            tooltip: {
                backgroundColor: "rgb(255,255,255)",
                bodyColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                padding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            }
        },
        cutout: '65%',
        elements: {
            center: {
                text: 'Total Pesanan\n' + jenisPesananData.reduce((a, b) => a + b.total, 0),
                color: '#5a5c69',
                fontStyle: 'Arial',
                fontSize: 16,
                lineHeight: 22
            }
        }
    }
});

// Chart 2: Diagram Batang - Menu Populer
const ctxMenu = document.getElementById('menuPopulerChart').getContext('2d');
const menuPopulerChart = new Chart(ctxMenu, {
    type: 'bar',
    data: {
        labels: menuPopulerData.map(item => item.menu ? item.menu.nama_menu : 'Unknown'),
        datasets: [{
            label: 'Jumlah Terjual',
            data: menuPopulerData.map(item => item.total_terjual),
            backgroundColor: '#4e73df',
            hoverBackgroundColor: '#2e59d9',
            borderColor: '#4e73df',
            borderWidth: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 10
                },
                maxBarThickness: 25,
            },
            y: {
                ticks: {
                    min: 0,
                    padding: 10,
                },
                grid: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            },
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                titleMarginBottom: 10,
                titleColor: '#6e707e',
                titleFont: {
                    size: 14
                },
                backgroundColor: "rgb(255,255,255)",
                bodyColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                padding: 15,
                displayColors: false,
                caretPadding: 10,
            }
        }
    }
});

// Chart 3: Line Chart - Pendapatan Mingguan
const ctxPendapatan = document.getElementById('pendapatanMingguanChart').getContext('2d');
const pendapatanMingguanChart = new Chart(ctxPendapatan, {
    type: 'line',
    data: {
        labels: pendapatanMingguanData.map(item => {
            const date = new Date(item.tanggal);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' });
        }),
        datasets: [{
            label: 'Pendapatan',
            data: pendapatanMingguanData.map(item => item.total),
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderColor: '#4e73df',
            borderWidth: 2,
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            },
            y: {
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return 'Rp ' + number_format(value);
                    }
                },
                grid: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            },
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: "rgb(255,255,255)",
                bodyColor: "#858796",
                titleMarginBottom: 10,
                titleColor: '#6e707e',
                titleFont: {
                    size: 14
                },
                borderColor: '#dddfeb',
                borderWidth: 1,
                padding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(context) {
                        return 'Pendapatan: Rp ' + number_format(context.parsed.y);
                    }
                }
            }
        }
    }
});

// Helper function untuk format number
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? ',' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
</script>
@endpush
@endsection