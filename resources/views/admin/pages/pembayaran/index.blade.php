@extends('admin.main')

@section('header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Kelola Pembayaran</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Pembayaran</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <style>
        /* Card Header Styling */
        .card-header.bg-primary {
            background: linear-gradient(45deg, #007bff, #0056b3) !important;
            color: white !important;
            border-bottom: none;
        }

        .card-header h4 {
            margin: 0;
            font-weight: 600;
        }

        /* Button Styling */
        .btn-success {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        /* Section Headers */
        .section-header {
            background: #343a40;
            color: white;
            padding: 10px 15px;
            margin: 0;
            font-weight: 600;
            font-size: 14px;
            border: none;
        }

        .section-header.dine-in {
            background: #ffc107;
            color: #212529;
        }

        .section-header.takeaway {
            background: #17a2b8;
            color: white;
        }

        /* Table Styling */
        .table {
            margin-bottom: 0;
            font-size: 13px;
        }

        .table thead th {
            background: #495057 !important;
            color: white !important;
            font-weight: 600;
            font-size: 12px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #dee2e6;
            padding: 8px 5px;
            white-space: nowrap;
        }

        .table tbody td {
            text-align: center;
            vertical-align: middle;
            padding: 8px 5px;
            border: 1px solid #dee2e6;
            font-size: 12px;
        }

        /* Badge Styling */
        .badge {
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 3px;
        }

        .badge-primary { background-color: #007bff !important; }
        .badge-success { background-color: #28a745 !important; }
        .badge-warning { background-color: #ffc107 !important; color: #212529 !important; }
        .badge-info { background-color: #17a2b8 !important; }
        .badge-secondary { background-color: #6c757d !important; }

        /* Status Badges */
        .status-dibayar { background-color: #28a745 !important; color: white; }
        .status-pending { background-color: #ffc107 !important; color: #212529; }
        .status-gagal { background-color: #dc3545 !important; color: white; }

        /* Method Badges */
        .method-qris { background-color: #17a2b8 !important; color: white; }
        .method-cash { background-color: #6f42c1 !important; color: white; }

        /* Button Action */
        .btn-sm {
            padding: 4px 8px;
            font-size: 11px;
            border-radius: 3px;
        }

        /* Responsive */
        .table-responsive {
            overflow-x: auto;
        }

        /* Empty State */
        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Card Shadow */
        .card {
            border-radius: 8px;
            overflow: hidden;
        }

        /* No data message */
        .no-data {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
        }

        /* Summary Stats */
        .summary-stats {
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 15px;
            margin: 0;
        }

        .stat-item {
            text-align: center;
            padding: 10px;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #495057;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            margin-top: 5px;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @php
                        // Ambil semua data pembayaran tanpa pagination
                        $allPembayarans = $pembayarans;
                        
                        // Jika $pembayarans adalah paginated collection, ambil semua item
                        if(method_exists($pembayarans, 'items')) {
                            // Untuk mendapatkan semua data, kita perlu menggunakan model langsung
                            $allPembayarans = App\Models\Pembayaran::orderBy('created_at', 'desc')->get();
                        }
                        
                        // Filter pembayaran berdasarkan jenis pesanan
                        $dineInPayments = $allPembayarans->filter(function($payment) {
                            $jenis = strtolower($payment->jenis_pesanan ?? '');
                            return in_array($jenis, ['dinein', 'dine-in', 'dine_in']);
                        });

                        $takeawayPayments = $allPembayarans->filter(function($payment) {
                            $jenis = strtolower($payment->jenis_pesanan ?? '');
                            return in_array($jenis, ['takeaway', 'take-away', 'take_away']);
                        });
                    @endphp

                    {{-- SECTION DINE-IN --}}
                    <div class="section-header dine-in">
                        🍽️ Dine-In ({{ $dineInPayments->count() }} transaksi)
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th style="width: 60px;">ID</th>
                                    <th style="width: 100px;">ID Pesanan</th>
                                    <th style="width: 120px;">Order ID</th>
                                    <th style="width: 100px;">Jenis Pesanan</th>
                                    <th style="width: 100px;">Nomor Meja</th>
                                    <th style="width: 100px;">Total Bayar</th>
                                    <th style="width: 120px;">Metode Pembayaran</th>
                                    <th style="width: 120px;">Status Pembayaran</th>
                                    <th style="width: 100px;">Status Pesanan</th>
                                    <th style="width: 80px;">Tanggal</th>
                                    <th style="width: 80px;">Waktu</th>
                                    <th style="width: 60px;">Aksi</th>
                                    <th style="width: 60px;">Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dineInPayments as $index => $pembayaran)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="badge badge-secondary">{{ $pembayaran->id }}</span></td>
                                        <td>
                                            @if ($pembayaran->pesanan_id)
                                                <span class="badge badge-primary">{{ $pembayaran->pesanan_id }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $pembayaran->order_id }}</small>
                                        </td>
                                        <td><span class="badge badge-warning">Dine-In</span></td>
                                        <td>
                                            @if($pembayaran->nomor_meja)
                                                <span class="badge badge-info">{{ $pembayaran->nomor_meja }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            @if($pembayaran->metode_pembayaran === 'qris')
                                                <span class="badge method-qris">QRIS</span>
                                            @elseif($pembayaran->metode_pembayaran === 'cash')
                                                <span class="badge method-cash">Cash</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($pembayaran->metode_pembayaran) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($pembayaran->status_pembayaran === 'dibayar')
                                                <span class="badge status-dibayar">Dibayar</span>
                                            @elseif($pembayaran->status_pembayaran === 'pending')
                                                <span class="badge status-pending">Pending</span>
                                            @else
                                                <span class="badge status-gagal">{{ ucfirst($pembayaran->status_pembayaran) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($pembayaran->pesanan) && $pembayaran->pesanan->status_pesanan === 'dibayar')
                                                <span class="badge status-dibayar">Dibayar</span>
                                            @elseif(isset($pembayaran->pesanan) && $pembayaran->pesanan->status_pesanan === 'pending')
                                                <span class="badge status-pending">Pending</span>
                                            @else
                                                <span class="badge badge-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($pembayaran->created_at)->format('d-m-Y') }}
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($pembayaran->created_at)->format('H:i') }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.pembayaran.edit', $pembayaran->id) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.pembayaran.invoice', $pembayaran->id) }}" 
                                               class="btn btn-info btn-sm" 
                                               target="_blank"
                                               title="Invoice">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="no-data">
                                            <i class="fas fa-inbox"></i><br>
                                            Tidak ada data pembayaran Dine-In
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <br>
                    <br>
                    <br>
                    {{-- SECTION TAKEAWAY --}}
                    <div class="section-header takeaway">
                        🥡 Takeaway ({{ $takeawayPayments->count() }} transaksi)
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th style="width: 60px;">ID</th>
                                    <th style="width: 100px;">ID Pesanan</th>
                                    <th style="width: 120px;">Order ID</th>
                                    <th style="width: 100px;">Jenis Pesanan</th>
                                    <th style="width: 120px;">Nama Pelanggan</th>
                                    <th style="width: 100px;">Nomor WA</th>
                                    <th style="width: 100px;">Total Bayar</th>
                                    <th style="width: 120px;">Metode Pembayaran</th>
                                    <th style="width: 120px;">Status Pembayaran</th>
                                    <th style="width: 100px;">Status Pesanan</th>
                                    <th style="width: 80px;">Tanggal</th>
                                    <th style="width: 80px;">Waktu</th>
                                    <th style="width: 60px;">Aksi</th>
                                    <th style="width: 60px;">Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($takeawayPayments as $index => $pembayaran)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="badge badge-secondary">{{ $pembayaran->id }}</span></td>
                                        <td>
                                            @if ($pembayaran->pesanan_id)
                                                <span class="badge badge-primary">{{ $pembayaran->pesanan_id }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $pembayaran->order_id }}</small>
                                        </td>
                                        <td><span class="badge badge-info">Takeaway</span></td>
                                        <td>{{ $pembayaran->nama_pelanggan ?? '-' }}</td>
                                        <td>
                                            @if($pembayaran->nomor_wa)
                                                <span class="badge badge-success">{{ $pembayaran->nomor_wa }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            @if($pembayaran->metode_pembayaran === 'qris')
                                                <span class="badge method-qris">QRIS</span>
                                            @elseif($pembayaran->metode_pembayaran === 'cash')
                                                <span class="badge method-cash">Cash</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($pembayaran->metode_pembayaran) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($pembayaran->status_pembayaran === 'dibayar')
                                                <span class="badge status-dibayar">Dibayar</span>
                                            @elseif($pembayaran->status_pembayaran === 'pending')
                                                <span class="badge status-pending">Pending</span>
                                            @else
                                                <span class="badge status-gagal">{{ ucfirst($pembayaran->status_pembayaran) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($pembayaran->pesanan) && $pembayaran->pesanan->status_pesanan === 'dibayar')
                                                <span class="badge status-dibayar">Dibayar</span>
                                            @elseif(isset($pembayaran->pesanan) && $pembayaran->pesanan->status_pesanan === 'pending')
                                                <span class="badge status-pending">Pending</span>
                                            @else
                                                <span class="badge badge-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($pembayaran->created_at)->format('d-m-Y') }}
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($pembayaran->created_at)->format('H:i') }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.pembayaran.edit', $pembayaran->id) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.pembayaran.invoice', $pembayaran->id) }}" 
                                               class="btn btn-info btn-sm" 
                                               target="_blank"
                                               title="Invoice">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="no-data">
                                            <i class="fas fa-inbox"></i><br>
                                            Tidak ada data pembayaran Takeaway
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary Statistics --}}
                    @if($dineInPayments->count() > 0 || $takeawayPayments->count() > 0)
                        <div class="summary-stats">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="stat-item">
                                        <div class="stat-number text-primary">{{ $dineInPayments->count() }}</div>
                                        <div class="stat-label">Dine-In</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-item">
                                        <div class="stat-number text-info">{{ $takeawayPayments->count() }}</div>
                                        <div class="stat-label">Takeaway</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-item">
                                        <div class="stat-number text-success">{{ $dineInPayments->count() + $takeawayPayments->count() }}</div>
                                        <div class="stat-label">Total Transaksi</div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-3">
                                    <div class="stat-item">
                                        <div class="stat-number text-warning">
                                            Rp {{ number_format($dineInPayments->sum('total_bayar') + $takeawayPayments->sum('total_bayar'), 0, ',', '.') }}
                                        </div>
                                        <div class="stat-label">Total Pendapatan</div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Kelola Pembayaran - Fixed Version Loaded');
    
    // Initialize tooltips
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[title]').tooltip({
            placement: 'top',
            trigger: 'hover'
        });
    }
    
    // Add hover effect to table rows
    const tableRows = document.querySelectorAll('tbody tr:not(.no-data)');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
    console.log('📊 Statistics:');
    console.log('- Total Dine-In:', {{ $dineInPayments->count() }});
    console.log('- Total Takeaway:', {{ $takeawayPayments->count() }});
    console.log('- All data displayed without pagination');
});
</script>
@endpush