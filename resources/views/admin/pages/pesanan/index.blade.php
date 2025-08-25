@extends('admin.main')

@section('header')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
    <div>
        <h1 class="mb-0">Pesanan</h1>
        <ol class="breadcrumb mt-1 mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Pesanan</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
{{-- CSRF Token untuk JavaScript --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ✅ Load Custom CSS dan JS --}}
<link rel="stylesheet" href="{{ asset('css/notification.css') }}">
<style>
/* Custom styles untuk UI sesuai gambar */
.order-type-tabs {
    background: #f8f9fa;
    padding: 0;
    border-radius: 8px;
    margin-bottom: 20px;
}

.order-type-btn {
    background: transparent;
    border: none;
    padding: 15px 30px;
    font-weight: 600;
    border-radius: 8px;
    margin: 0;
    color: #666;
    transition: all 0.3s ease;
}

.order-type-btn.active {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.order-type-btn.dinein.active {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: #000;
}

.order-type-btn.takeaway.active {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
}

.table-section {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 30px;
}

.table-header {
    padding: 15px 20px;
    font-weight: bold;
    color: white;
    display: flex;
    align-items: center;
    gap: 10px;
}

.dinein-header {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: #000;
}

.takeaway-header {
    background: linear-gradient(135deg, #17a2b8, #138496);
}

.table-responsive {
    max-height: 500px;
    overflow-y: auto;
}

.table thead th {
    background-color: #495057;
    color: white;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    border: none;
    padding: 12px 8px;
    font-size: 12px;
}

.table tbody td {
    text-align: center;
    vertical-align: middle;
    padding: 12px 8px;
    border: 1px solid #dee2e6;
}

.badge {
    font-size: 11px;
    padding: 6px 10px;
}

.btn-action {
    padding: 5px 10px;
    font-size: 11px;
    margin: 2px;
}

.notification-controls {
    background: white;
    padding: 10px 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .order-type-btn {
        padding: 10px 15px;
        font-size: 14px;
    }
    
    .table-responsive {
        font-size: 12px;
    }
}
</style>

<div class="container-fluid">
    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Control Panel --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                {{-- Add New Order Button --}}
                {{-- <div>
                    <a href="{{ route('admin.pesanan.create') }}" class="btn btn-success btn-lg shadow-sm">
                        <i class="fas fa-plus"></i> Tambah Pesanan
                    </a>
                </div> --}}

                {{-- Notification Status --}}
                <div class="notification-controls d-flex align-items-center gap-3">
                    <span id="notifStatus" class="badge badge-info status-indicator">
                        🔄 Memulai Sistem...
                    </span>
                    <button id="testNotifBtn" class="btn btn-sm btn-outline-secondary" title="Test Notifikasi">
                        🔊 Test
                    </button>
                    <small id="lastCheck" class="text-muted"></small>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Auto-refresh Indicator --}}
    <div id="autoRefreshIndicator" class="alert alert-success border-left-success" style="display: none;">
        <div class="d-flex align-items-center">
            <div class="loading-indicator me-3"></div>
            <div>
                <strong>🎉 Pesanan Baru Terdeteksi!</strong>
                <br>
                <small>Data baru telah ditambahkan ke tabel...</small>
            </div>
        </div>
    </div>

    {{-- ✅ Connection Status Indicator --}}
    <div class="d-flex justify-content-center mb-3">
        <div id="connectionStatus" class="connection-status online">
            <span class="status-dot"></span>
            <span class="status-text">🌐 Terhubung</span>
        </div>
    </div>

    {{-- Order Type Tabs --}}

    {{-- ✅ DINE-IN Section --}}
    @if(!request('jenis') || request('jenis') === 'dinein')
    <div class="table-section" id="dinein-section">
        <div class="table-header dinein-header">
            🍽️ Dine-In
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="dineinTable">
                <thead>
                    <tr>
                        <th>Tanggal Pesanan</th>
                        <th>Waktu Pesanan</th>
                        <th>Jenis Pesanan</th>
                        <th>Nomor Meja</th>
                        <th>Tipe Meja</th>
                        <th>Lantai</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="dineinTableBody">
                    @php 
                        $dineinPesanans = $pesanans->filter(function($pesanan) {
                            return strtolower(str_replace('-', '', $pesanan->jenis_pesanan)) === 'dinein';
                        });
                    @endphp
                    
                    @forelse ($dineinPesanans as $pesanan)
                        <tr data-pesanan-id="{{ $pesanan->id }}" 
                            data-created="{{ $pesanan->created_at->timestamp }}"
                            class="pesanan-row">
                            <td>{{ $pesanan->tanggal_pesanan }}</td>
                            <td>{{ $pesanan->waktu_pesanan }}</td>
                            <td>
                                <span class="badge badge-warning">📋 Dine-In</span>
                            </td>
                            <td>{{ $pesanan->meja->nomor_meja ?? '-' }}</td>
                            <td>{{ $pesanan->meja->tipe_meja ?? '-' }}</td>
                            <td>{{ $pesanan->meja->lantai ?? '-' }}</td>
                            <td><strong>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong></td>
                            <td>
                                @if($pesanan->metode_pembayaran === 'qris')
                                    <span class="badge badge-info">💳 QRIS</span>
                                @elseif($pesanan->metode_pembayaran === 'cash')
                                    <span class="badge badge-success">💵 Cash</span>
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = 'badge-secondary';
                                    $statusText = ucfirst($pesanan->status_pesanan);
                                    
                                    switch($pesanan->status_pesanan) {
                                        case 'pending':
                                            $statusClass = 'badge-warning';
                                            break;
                                        case 'dibayar':
                                            $statusClass = 'badge-primary';
                                            break;
                                        case 'selesai':
                                            $statusClass = 'badge-success';
                                            break;
                                        case 'dibatalkan':
                                            $statusClass = 'badge-danger';
                                            break;
                                    }
                                @endphp
                                
                                <span class="badge {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center" style="gap: 4px;">
                                    <a href="{{ route('admin.pesanan.edit', $pesanan->id) }}" 
                                       class="btn btn-sm btn-warning btn-action" title="Edit">
                                        ✏️
                                    </a>
                                    <form action="{{ route('admin.pesanan.destroy', $pesanan->id) }}" 
                                          method="POST" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirmDelete(this, '{{ $pesanan->id }}', 'dinein')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger btn-action" 
                                                title="Hapus"
                                                {{ $pesanan->status_pesanan === 'dibayar' ? 'disabled' : '' }}>
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-muted text-center py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Tidak ada data pesanan Dine-In
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ✅ TAKEAWAY Section --}}
    @if(!request('jenis') || request('jenis') === 'takeaway')
    <div class="table-section" id="takeaway-section">
        <div class="table-header takeaway-header">
            🥡 Takeaway
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="takeawayTable">
                <thead>
                    <tr>
                        <th>Tanggal Pesanan</th>
                        <th>Waktu Pesanan</th>
                        <th>Jenis Pesanan</th>
                        <th>Nama Pelanggan</th>
                        <th>Nomor WhatsApp</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="takeawayTableBody">
                    @php 
                        $takeawayPesanans = $pesanans->filter(function($pesanan) {
                            return strtolower(str_replace('-', '', $pesanan->jenis_pesanan)) === 'takeaway';
                        });
                    @endphp
                    
                    @forelse ($takeawayPesanans as $pesanan)
                        <tr data-pesanan-id="{{ $pesanan->id }}" 
                            data-created="{{ $pesanan->created_at->timestamp }}"
                            class="pesanan-row">
                            <td>{{ $pesanan->tanggal_pesanan }}</td>
                            <td>{{ $pesanan->waktu_pesanan }}</td>
                            <td>
                                <span class="badge badge-info">🥡 Takeaway</span>
                            </td>
                            <td>{{ $pesanan->nama_pelanggan ?? '-' }}</td>
                            <td>{{ $pesanan->nomor_wa ?? '-' }}</td>
                            <td><strong>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong></td>
                            <td>
                                @if($pesanan->metode_pembayaran === 'qris')
                                    <span class="badge badge-info">💳 QRIS</span>
                                @elseif($pesanan->metode_pembayaran === 'cash')
                                    <span class="badge badge-success">💵 Cash</span>
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = 'badge-secondary';
                                    $statusText = ucfirst($pesanan->status_pesanan);
                                    
                                    switch($pesanan->status_pesanan) {
                                        case 'pending':
                                            $statusClass = 'badge-warning';
                                            break;
                                        case 'dibayar':
                                            $statusClass = 'badge-primary';
                                            break;
                                        case 'selesai':
                                            $statusClass = 'badge-success';
                                            break;
                                        case 'dibatalkan':
                                            $statusClass = 'badge-danger';
                                            break;
                                    }
                                @endphp
                                
                                <span class="badge {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center" style="gap: 4px;">
                                    <a href="{{ route('admin.pesanan.edit', $pesanan->id) }}" 
                                       class="btn btn-sm btn-warning btn-action" title="Edit">
                                        ✏️
                                    </a>
                                    <form action="{{ route('admin.pesanan.destroy', $pesanan->id) }}" 
                                          method="POST" 
                                          style="display: inline-block;" 
                                          onsubmit="return confirmDelete(this, '{{ $pesanan->id }}', 'takeaway')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger btn-action" 
                                                title="Hapus"
                                                {{ $pesanan->status_pesanan === 'dibayar' ? 'disabled' : '' }}>
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted text-center py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Tidak ada data pesanan Takeaway
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- 🔔 Audio Notifikasi dengan durasi 16 detik --}}
<audio id="notifAudio" preload="auto" style="display: none;">
    <source src="{{ secure_asset('sounds/notifpesanan.mp3') }}" type="audio/mpeg">
    <source src="{{ asset('sounds/notifpesanan.mp3') }}" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>

{{-- ✅ Debug Panel (Hidden by default) --}}
<div id="debugPanel" class="card mt-4" style="display: none;">
    <div class="card-header bg-dark text-white">
        <h5>🐛 Debug Panel</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Audio Status</h6>
                <div id="audioStatus"></div>
            </div>
            <div class="col-md-6">
                <h6>Polling Status</h6>
                <div id="pollingStatus"></div>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-sm btn-primary" onclick="window.debugNotification.forceCheck()">Force Check</button>
            <button class="btn btn-sm btn-success" onclick="window.debugNotification.testDinein()">Test Dine-in</button>
            <button class="btn btn-sm btn-info" onclick="window.debugNotification.testTakeaway()">Test Takeaway</button>
            <button class="btn btn-sm btn-warning" onclick="window.debugAudio.testFull()">Test Full Audio</button>
        </div>
    </div>
</div>

<script>
// ✅ PERBAIKAN: Filter function yang lebih robust
function filterPesanan(jenis) {
    console.log('🔍 Filtering pesanan:', jenis);
    
    // Update URL dengan parameter filter
    const url = new URL(window.location.href);
    if (jenis) {
        url.searchParams.set('jenis', jenis);
    } else {
        url.searchParams.delete('jenis');
    }
    
    // Redirect ke URL yang sudah difilter
    window.location.href = url.toString();
}

// ✅ PERBAIKAN: Enhanced delete confirmation
function confirmDelete(form, pesananId, jenis) {
    console.log('🗑️ Delete confirmation for:', pesananId, jenis);
    
    // Check if order is paid (button should be disabled but double check)
    const deleteButton = form.querySelector('button[type="submit"]');
    if (deleteButton && deleteButton.disabled) {
        alert('Pesanan yang sudah dibayar tidak dapat dihapus!');
        return false;
    }
    
    const jenisText = jenis === 'dinein' ? 'Dine-In' : 'Takeaway';
    const confirmMessage = `Yakin ingin menghapus pesanan ${jenisText} ID: ${pesananId}?\n\nTindakan ini tidak dapat dibatalkan!`;
    
    if (confirm(confirmMessage)) {
        // Add loading state
        deleteButton.innerHTML = '⏳';
        deleteButton.disabled = true;
        
        console.log('✅ Delete confirmed for pesanan:', pesananId);
        return true;
    }
    
    console.log('❌ Delete cancelled for pesanan:', pesananId);
    return false;
}

// Show/hide sections based on current filter
document.addEventListener('DOMContentLoaded', function() {
    const currentFilter = "{{ request('jenis') ?? '' }}";
    console.log('🎯 Current filter on page load:', currentFilter);
    
    // Hide sections based on filter
    if (currentFilter === 'dinein') {
        const takeawaySection = document.getElementById('takeaway-section');
        if (takeawaySection) takeawaySection.style.display = 'none';
    } else if (currentFilter === 'takeaway') {
        const dineinSection = document.getElementById('dinein-section');
        if (dineinSection) dineinSection.style.display = 'none';
    }
    
    // Update active tab
    document.querySelectorAll('.order-type-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    if (currentFilter) {
        const activeBtn = document.querySelector(`.order-type-btn.${currentFilter}`);
        if (activeBtn) activeBtn.classList.add('active');
    }
});
</script>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/notification-helper.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initializing Enhanced Real-time Notification System...');
    
    // ===== ✅ KONFIGURASI YANG DIPERBAIKI =====
    const CONFIG = {
        POLLING_INTERVAL: 3000,    // 3 detik untuk responsivitas
        RETRY_INTERVAL: 10000,     // 10 detik untuk retry
        AUDIO_VOLUME: 0.8,
        POPUP_DURATION: 7000,      // ✅ 7 detik untuk popup
        AUDIO_DURATION: 16000,     // ✅ 16 detik untuk audio
        REFRESH_DELAY: 8000,       // 8 detik delay sebelum refresh
        MAX_RETRY_ATTEMPTS: 3,     // Maksimal retry
        NOTIFICATION_COOLDOWN: 3000 // 3 detik cooldown antar notifikasi
    };

    // ===== ELEMENTS =====
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const notifAudio = document.getElementById('notifAudio');
    const notifStatus = document.getElementById('notifStatus');
    const lastCheck = document.getElementById('lastCheck');
    const testNotifBtn = document.getElementById('testNotifBtn');
    const autoRefreshIndicator = document.getElementById('autoRefreshIndicator');
    const connectionStatus = document.getElementById('connectionStatus');
    const dineinTableBody = document.getElementById('dineinTableBody');
    const takeawayTableBody = document.getElementById('takeawayTableBody');

    if (!csrfToken) {
        console.error('❌ CSRF token not found!');
        return;
    }

    // ===== STATE VARIABLES =====
    let isPolling = true;
    let pollingInterval;
    let audioEnabled = false;
    let lastNotificationTime = 0;
    let currentAudioTimeout = null;
    let retryAttempts = 0;
    let knownPesananIds = new Set();
    
    const jenisPesanan = "{{ request('jenis') ?? '' }}";
    console.log('🎯 Current filter jenis pesanan:', jenisPesanan);
    
    // Initialize known pesanan IDs from current tables
    document.querySelectorAll('[data-pesanan-id]').forEach(row => {
        knownPesananIds.add(parseInt(row.dataset.pesananId));
    });
    
    console.log('📋 Initial known pesanan IDs:', Array.from(knownPesananIds));

    // ===== ✅ ENHANCED AUDIO MANAGEMENT =====
    function enableAudio() {
        if (!audioEnabled && window.notificationHelper) {
            window.notificationHelper.enableAudio().then(() => {
                audioEnabled = true;
                console.log('✅ Audio enabled successfully via helper');
                updateNotificationStatus('active', '🔊 Audio Ready');
            }).catch(err => {
                console.warn('⚠️ Audio enable failed via helper:', err.message);
                audioEnabled = true; // Set true untuk mencoba lagi nanti
                updateNotificationStatus('active', '🔇 Audio Loading');
            });
        } else if (!audioEnabled && notifAudio) {
            // Fallback ke audio element biasa
            notifAudio.volume = CONFIG.AUDIO_VOLUME;
            notifAudio.play().then(() => {
                notifAudio.pause();
                notifAudio.currentTime = 0;
                audioEnabled = true;
                console.log('✅ Fallback audio enabled successfully');
                updateNotificationStatus('active', '🔊 Fallback Audio Ready');
            }).catch(err => {
                console.warn('⚠️ Fallback audio enable failed:', err.message);
                audioEnabled = true;
                updateNotificationStatus('active', '🔇 Audio Loading');
            });
        }
    }

    // Enable audio on first user interaction
    const enableAudioEvents = ['click', 'keydown', 'touchstart', 'scroll', 'mousemove'];
    enableAudioEvents.forEach(event => {
        document.addEventListener(event, enableAudio, { once: true });
    });

    // ✅ PERBAIKAN: Audio dengan durasi 16 detik menggunakan helper
    async function playNotificationSound(duration = CONFIG.AUDIO_DURATION) {
        if (!audioEnabled) {
            console.warn('⚠️ Audio not enabled');
            return Promise.resolve();
        }

        try {
            // Prioritas menggunakan notification helper
            if (window.notificationHelper) {
                console.log(`🔊 Playing via notification helper (${duration}ms)`);
                const success = await window.notificationHelper.playNotification(CONFIG.AUDIO_VOLUME, duration);
                if (success) {
                    return;
                }
            }

            // Fallback ke audio element dengan timeout manual
            if (notifAudio) {
                console.log(`🔊 Playing via audio element (${duration}ms)`);
                
                if (currentAudioTimeout) {
                    clearTimeout(currentAudioTimeout);
                }

                notifAudio.currentTime = 0;
                notifAudio.volume = CONFIG.AUDIO_VOLUME;
                
                await notifAudio.play();
                
                currentAudioTimeout = setTimeout(() => {
                    try {
                        notifAudio.pause();
                        notifAudio.currentTime = 0;
                        console.log('🔇 Audio stopped after', duration + 'ms');
                    } catch (e) {
                        console.warn('⚠️ Error stopping audio:', e.message);
                    }
                }, duration);
            }
        } catch (error) {
            console.warn('⚠️ Audio error:', error.message);
        }
    }

    // ===== ✅ ENHANCED NOTIFICATION DISPLAY sesuai gambar =====
    function showNotification(pesananData) {
        // Prevent spam notifications
        const now = Date.now();
        if (now - lastNotificationTime < CONFIG.NOTIFICATION_COOLDOWN) {
            console.log('🚫 Notification blocked by cooldown');
            return;
        }
        lastNotificationTime = now;

        let titleText = '';
        let htmlContent = '';
        let iconType = 'success';

        if (pesananData) {
            // ✅ PERBAIKAN: Deteksi jenis pesanan yang lebih robust
            const jenisPesananData = pesananData.jenis_pesanan.toLowerCase().replace(/[-_]/g, '');
            
            if (jenisPesananData === 'dinein') {
                titleText = '🍽️ Pesanan Dine-In Baru!';
                htmlContent = `
                    <div class="notification-popup-content">
                        <div class="order-details-box">
                            <div class="detail-row">
                                <strong>Nomor Meja</strong> : <span>${pesananData.nomor_meja || '-'}</span>
                            </div>
                            <div class="detail-row">
                                <strong>Tipe Meja</strong> : <span>${pesananData.tipe_meja || '-'}</span>
                            </div>
                            <div class="detail-row">
                                <strong>Lantai</strong> : <span>${pesananData.lantai || '-'}</span>
                            </div>
                            <div class="detail-row">
                                <strong>Pembayaran</strong> : <span>${pesananData.pembayaran || pesananData.metode_pembayaran || 'QRIS / CASH'}</span>
                            </div>
                        </div>
                        <div class="total-amount">
                            Total: <strong>Rp ${new Intl.NumberFormat('id-ID').format(pesananData.total_harga)}</strong>
                        </div>
                    </div>
                `;
            } else if (jenisPesananData === 'takeaway') {
                titleText = '🥡 Pesanan Takeaway Baru!';
                htmlContent = `
                    <div class="notification-popup-content">
                        <div class="order-details-box">
                            <div class="detail-row">
                                <strong>Nama Pelanggan</strong> : <span>${pesananData.nama_pelanggan || '-'}</span>
                            </div>
                            <div class="detail-row">
                                <strong>Nomor WA</strong> : <span>${pesananData.nomor_wa || '-'}</span>
                            </div>
                            <div class="detail-row">
                                <strong>Pembayaran</strong> : <span>${pesananData.pembayaran || pesananData.metode_pembayaran || 'QRIS'}</span>
                            </div>
                        </div>
                        <div class="total-amount">
                            Total: <strong>Rp ${new Intl.NumberFormat('id-ID').format(pesananData.total_harga)}</strong>
                        </div>
                    </div>
                `;
            }
        } else {
            titleText = '📥 Pesanan Baru Masuk!';
            htmlContent = '<p>Ada pesanan baru yang masuk ke sistem!</p>';
        }

        // ✅ Play sound IMMEDIATELY ketika notifikasi muncul dengan durasi 16 detik
        playNotificationSound(CONFIG.AUDIO_DURATION);

        // ✅ Show SweetAlert notification dengan desain sesuai gambar (7 detik)
        Swal.fire({
            title: titleText,
            html: htmlContent,
            icon: iconType,
            confirmButtonText: '👀 Lihat Pesanan',
            confirmButtonColor: '#28a745',
            cancelButtonText: 'Tutup',
            showCancelButton: true,
            timer: CONFIG.POPUP_DURATION, // ✅ 7 detik untuk popup
            timerProgressBar: true,
            allowOutsideClick: false,
            allowEscapeKey: true,
            width: 450,
            customClass: {
                popup: 'custom-notification-popup',
                confirmButton: 'btn btn-success btn-lg',
                cancelButton: 'btn btn-secondary btn-lg',
                title: 'notification-title',
                htmlContainer: 'notification-content'
            },
            showClass: {
                popup: 'animate__animated animate__bounceInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__zoomOut animate__faster'
            },
            backdrop: `
                rgba(0, 123, 255, 0.4)
                left top
                no-repeat
            `
        }).then((result) => {
            if (result.isConfirmed) {
                // Refresh halaman untuk menampilkan pesanan terbaru
                window.location.reload();
            }
        });

        console.log('🔔 Enhanced notification displayed for:', pesananData);
    }

    // ===== ✅ ENHANCED STATUS MANAGEMENT =====
    function updateNotificationStatus(status, extraMessage = '') {
        const statusConfig = {
            'initializing': { text: '🔄 Memulai Sistem', class: 'badge-info', icon: '⚙️' },
            'active': { text: '🟢 Notifikasi Aktif', class: 'badge-success', icon: '✅' },
            'checking': { text: '🔍 Memeriksa', class: 'badge-primary', icon: '👁️' },
            'paused': { text: '⏸️ Dijeda', class: 'badge-warning', icon: '⏸️' },
            'error': { text: '❌ Error', class: 'badge-danger', icon: '⚠️' },
            'new_order': { text: '🔥 PESANAN BARU!', class: 'badge-success animate__animated animate__pulse animate__infinite', icon: '🎉' }
        };

        if (notifStatus && statusConfig[status]) {
            const config = statusConfig[status];
            const displayText = `${config.icon} ${config.text}${extraMessage ? ` ${extraMessage}` : ''}`;
            
            notifStatus.textContent = displayText;
            notifStatus.className = `badge ${config.class} status-indicator`;
            
            // Add special effects for new order
            if (status === 'new_order') {
                notifStatus.style.animation = 'pulse-success 0.5s infinite, glow 1s ease-in-out infinite alternate';
            } else {
                notifStatus.style.animation = '';
            }
        }

        // Update last check time dengan format yang lebih baik
        if (lastCheck) {
            const now = new Date();
            lastCheck.textContent = `Cek terakhir: ${now.toLocaleTimeString('id-ID')}`;
            lastCheck.title = `Terakhir dicek pada: ${now.toLocaleString('id-ID')}`;
        }

        // Update connection status
        updateConnectionStatus(status !== 'error');
    }

    // ✅ Connection status management
    function updateConnectionStatus(isOnline) {
        if (!connectionStatus) return;

        if (isOnline) {
            connectionStatus.className = 'connection-status online';
            connectionStatus.innerHTML = '<span class="status-dot"></span><span class="status-text">🌐 Terhubung</span>';
        } else {
            connectionStatus.className = 'connection-status offline';
            connectionStatus.innerHTML = '<span class="status-dot"></span><span class="status-text">🔴 Terputus</span>';
        }
    }

    // ===== ✅ PERBAIKAN: ADD NEW ROW FUNCTION DENGAN TABEL TERPISAH --}}
    function addNewRowToTable(pesananData) {
        // ✅ PERBAIKAN: Normalisasi jenis pesanan untuk perbandingan
        const jenisPesananData = pesananData.jenis_pesanan.toLowerCase().replace(/[-_]/g, '');
        const currentFilter = jenisPesanan;

        console.log('🔍 Filter check:', {
            jenisPesananData,
            currentFilter,
            shouldShow: !currentFilter || currentFilter === jenisPesananData
        });

        // Check if this pesanan should be displayed based on current filter
        if (currentFilter && currentFilter !== jenisPesananData) {
            console.log('❌ Pesanan tidak ditampilkan karena filter tidak sesuai:', currentFilter, 'vs', jenisPesananData);
            return;
        }

        // Pilih tabel yang tepat berdasarkan jenis pesanan
        let targetTableBody;
        if (jenisPesananData === 'dinein') {
            targetTableBody = dineinTableBody;
        } else if (jenisPesananData === 'takeaway') {
            targetTableBody = takeawayTableBody;
        }

        if (!targetTableBody) {
            console.warn('❌ Target table body not found for:', jenisPesananData);
            return;
        }

        // Create new row
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-pesanan-id', pesananData.id);
        newRow.setAttribute('data-created', pesananData.created_timestamp);
        newRow.className = 'pesanan-row new-pesanan';

        let rowHTML = `
            <td>${pesananData.tanggal_pesanan}</td>
            <td>${pesananData.waktu_pesanan}</td>
            <td>
                ${jenisPesananData === 'dinein' ? 
                    '<span class="badge badge-warning">📋 Dine-In</span>' : 
                    '<span class="badge badge-info">🥡 Takeaway</span>'
                }
            </td>
        `;

        // ✅ PERBAIKAN: Add columns based on order type
        if (jenisPesananData === 'dinein') {
            // Kolom untuk dine-in
            rowHTML += `
                <td>${pesananData.nomor_meja || '-'}</td>
                <td>${pesananData.tipe_meja || '-'}</td>
                <td>${pesananData.lantai || '-'}</td>
            `;
        } else if (jenisPesananData === 'takeaway') {
            // Kolom untuk takeaway
            rowHTML += `
                <td>${pesananData.nama_pelanggan || '-'}</td>
                <td>${pesananData.nomor_wa || '-'}</td>
            `;
        }

        // Tambahkan kolom total, pembayaran, status, dan aksi
        rowHTML += `
            <td><strong>Rp ${new Intl.NumberFormat('id-ID').format(pesananData.total_harga)}</strong></td>
            <td>
                ${pesananData.metode_pembayaran === 'qris' ? 
                    '<span class="badge badge-info">💳 QRIS</span>' : 
                    (pesananData.metode_pembayaran === 'cash' ? 
                        '<span class="badge badge-success">💵 Cash</span>' : 
                        '<span class="badge badge-secondary">-</span>'
                    )
                }
            </td>
            <td>
                <span class="badge ${pesananData.status_pesanan === 'pending' ? 'badge-warning' : 'badge-secondary'}">
                    ${pesananData.status_pesanan.charAt(0).toUpperCase() + pesananData.status_pesanan.slice(1)}
                </span>
            </td>
            <td>
                <div class="d-flex justify-content-center" style="gap: 4px;">
                    <a href="/admin/pesanan/${pesananData.id}/edit" class="btn btn-sm btn-warning btn-action" title="Edit">✏️</a>
                    <form action="/admin/pesanan/${pesananData.id}" method="POST" style="display: inline-block;" onsubmit="return confirmDelete(this, '${pesananData.id}', '${jenisPesananData}')">
                        <input type="hidden" name="_token" value="${csrfToken.content}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-sm btn-danger btn-action" title="Hapus">🗑️</button>
                    </form>
                </div>
            </td>
        `;

        newRow.innerHTML = rowHTML;

        // Add to beginning of appropriate table body
        if (targetTableBody.children.length > 0) {
            // Check if there's an empty state row to remove
            const emptyRow = targetTableBody.querySelector('td[colspan]');
            if (emptyRow) {
                emptyRow.parentElement.remove();
            }
            targetTableBody.insertBefore(newRow, targetTableBody.firstChild);
        } else {
            targetTableBody.appendChild(newRow);
        }

        // Add to known IDs
        knownPesananIds.add(pesananData.id);

        console.log(`✅ New row added to ${jenisPesananData} table:`, pesananData.id);
    }

    // ===== ✅ ENHANCED MAIN POLLING FUNCTION =====
    function checkForNewOrders() {
        if (!isPolling) {
            console.log('⏸️ Polling is paused');
            return;
        }

        updateNotificationStatus('checking');
        
        // Build URL dengan cache buster yang lebih kuat
        const protocol = window.location.protocol;
        const host = window.location.host;
        const baseUrl = `${protocol}//${host}/admin/pesanan/check-new-pesanan`;
        
        const params = new URLSearchParams();
        // Don't filter by jenis in polling - we want to get all new orders
        // if (jenisPesanan) {
        //     params.append('jenis', jenisPesanan);
        // }
        params.append('_t', Date.now()); // Cache buster
        params.append('_r', Math.random().toString(36).substring(7)); // Additional randomness
        
        const url = `${baseUrl}?${params.toString()}`;

        console.log('🔍 Checking for new orders:', url);

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0',
                'X-CSRF-TOKEN': csrfToken.content
            },
            cache: 'no-store',
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('📡 Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📋 Response data:', data);
            retryAttempts = 0; // Reset retry counter on success
            
            if (data.new_pesanan && data.pesanan_data) {
                console.log('🔔 NEW ORDER DETECTED!', data.pesanan_data);
                
                // Check if this is truly a new pesanan
                if (!knownPesananIds.has(data.pesanan_data.id)) {
                    // Update status to show new order
                    updateNotificationStatus('new_order', `ID: ${data.pesanan_data.id}`);
                    
                    // Show notification immediately
                    showNotification(data.pesanan_data);
                    
                    // Add new row to appropriate table
                    addNewRowToTable(data.pesanan_data);
                    
                    // Show auto-refresh indicator dengan animasi
                    if (autoRefreshIndicator) {
                        autoRefreshIndicator.style.display = 'block';
                        autoRefreshIndicator.classList.add('animate__animated', 'animate__slideInDown');
                    }
                    
                } else {
                    console.log('🔍 Pesanan sudah dikenal, tidak perlu notifikasi:', data.pesanan_data.id);
                }
            } else {
                // Back to normal status
                updateNotificationStatus('active', '✓ Sistem Normal');
                
                // Hide auto-refresh indicator
                if (autoRefreshIndicator) {
                    autoRefreshIndicator.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('❌ Polling error:', error);
            retryAttempts++;
            
            updateNotificationStatus('error', `${error.message.substring(0, 20)} (${retryAttempts}/${CONFIG.MAX_RETRY_ATTEMPTS})`);
            
            // Hide auto-refresh indicator on error
            if (autoRefreshIndicator) {
                autoRefreshIndicator.style.display = 'none';
            }
            
            // Stop polling if max retries reached
            if (retryAttempts >= CONFIG.MAX_RETRY_ATTEMPTS) {
                console.error('❌ Max retry attempts reached, stopping polling');
                stopPolling();
                updateNotificationStatus('error', '❌ Connection Lost');
            } else {
                // Retry after delay dengan exponential backoff
                const retryDelay = CONFIG.RETRY_INTERVAL * Math.pow(2, retryAttempts - 1);
                setTimeout(() => {
                    if (isPolling) {
                        updateNotificationStatus('active', '↻ Reconnecting');
                    }
                }, retryDelay);
            }
        });
    }

    // ===== POLLING CONTROL =====
    function startPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
        
        pollingInterval = setInterval(checkForNewOrders, CONFIG.POLLING_INTERVAL);
        isPolling = true;
        retryAttempts = 0; // Reset retry counter
        updateNotificationStatus('active', '🚀 Started');
        
        console.log(`🚀 Enhanced notification polling started (${CONFIG.POLLING_INTERVAL}ms interval)`);
        
        // Initial check setelah delay singkat
        setTimeout(checkForNewOrders, 1500);
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
        isPolling = false;
        updateNotificationStatus('paused', '💤 Stopped');
        
        console.log('⏸️ Notification polling stopped');
    }

    // ===== EVENT HANDLERS =====
    
    // Visibility change handler (pause when tab not visible)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            console.log('📴 Tab hidden - pausing notifications');
            // Don't completely stop, just pause temporarily
            if (isPolling) {
                updateNotificationStatus('paused', '👁️ Tab Hidden');
            }
        } else {
            console.log('👁️ Tab visible - resuming notifications');
            if (isPolling) {
                updateNotificationStatus('active', '👁️ Tab Active');
                // Force immediate check when tab becomes visible
                setTimeout(checkForNewOrders, 500);
            }
        }
    });

    // ✅ Enhanced test notification button
    if (testNotifBtn) {
        testNotifBtn.addEventListener('click', function() {
            console.log('🧪 Testing enhanced notification system...');
            enableAudio();
            
            // Test dengan data dummy yang lebih realistis berdasarkan filter
            let testData;
            
            if (jenisPesanan === 'dinein' || (!jenisPesanan && Math.random() > 0.5)) {
                testData = {
                    id: 9999,
                    jenis_pesanan: 'dinein',
                    nomor_meja: '05',
                    tipe_meja: 'Lesehan',
                    lantai: '2',
                    metode_pembayaran: 'qris',
                    pembayaran: 'QRIS',
                    total_harga: 125000,
                    status_pesanan: 'pending',
                    tanggal_pesanan: new Date().toISOString().split('T')[0],
                    waktu_pesanan: new Date().toTimeString().split(' ')[0],
                    created_timestamp: Date.now()
                };
            } else {
                testData = {
                    id: 9999,
                    jenis_pesanan: 'takeaway',
                    nama_pelanggan: 'Dimas Nur',
                    nomor_wa: '081234567890',
                    metode_pembayaran: 'qris',
                    pembayaran: 'QRIS',
                    total_harga: 75000,
                    status_pesanan: 'pending',
                    tanggal_pesanan: new Date().toISOString().split('T')[0],
                    waktu_pesanan: new Date().toTimeString().split(' ')[0],
                    created_timestamp: Date.now()
                };
            }
            
            // Update button state
            testNotifBtn.textContent = '🧪 Testing...';
            testNotifBtn.disabled = true;
            
            showNotification(testData);
            
            // Reset button after test
            setTimeout(() => {
                testNotifBtn.textContent = '🔊 Test';
                testNotifBtn.disabled = false;
            }, 3000);
        });
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        stopPolling();
        if (currentAudioTimeout) {
            clearTimeout(currentAudioTimeout);
        }
        if (window.notificationHelper) {
            window.notificationHelper.stopNotification();
        }
    });

    // ===== ✅ ENHANCED DEBUG FUNCTIONS (Global) =====
    window.debugNotification = {
        testSound: function(duration = CONFIG.AUDIO_DURATION) {
            console.log(`🎵 Testing notification sound (${duration}ms)...`);
            enableAudio();
            return playNotificationSound(duration);
        },
        
        testDinein: function() {
            console.log('🧪 Testing Dine-in notification...');
            showNotification({
                id: 8888,
                jenis_pesanan: 'dinein',
                nomor_meja: '12',
                tipe_meja: 'Premium',
                lantai: '1',
                metode_pembayaran: 'cash',
                pembayaran: 'CASH',
                total_harga: 85000,
                status_pesanan: 'pending',
                tanggal_pesanan: new Date().toISOString().split('T')[0],
                waktu_pesanan: new Date().toTimeString().split(' ')[0],
                created_timestamp: Date.now()
            });
        },
        
        testTakeaway: function() {
            console.log('🧪 Testing Takeaway notification...');
            showNotification({
                id: 7777,
                jenis_pesanan: 'takeaway',
                nama_pelanggan: 'Jane Smith',
                nomor_wa: '089876543210',
                metode_pembayaran: 'qris',
                pembayaran: 'QRIS',
                total_harga: 65000,
                status_pesanan: 'pending',
                tanggal_pesanan: new Date().toISOString().split('T')[0],
                waktu_pesanan: new Date().toTimeString().split(' ')[0],
                created_timestamp: Date.now()
            });
        },
        
        forceCheck: function() {
            console.log('🔄 Force checking for new orders...');
            checkForNewOrders();
        },
        
        getStatus: function() {
            return {
                isPolling,
                audioEnabled,
                jenisPesanan,
                pollingInterval: CONFIG.POLLING_INTERVAL,
                popupDuration: CONFIG.POPUP_DURATION,
                audioDuration: CONFIG.AUDIO_DURATION,
                lastNotificationTime,
                retryAttempts,
                knownPesananCount: knownPesananIds.size,
                knownPesananIds: Array.from(knownPesananIds),
                config: CONFIG
            };
        },
        
        clearKnownIds: function() {
            knownPesananIds.clear();
            console.log('🗑️ Known pesanan IDs cleared');
        }
    };

    // ===== ✅ INITIALIZATION =====
    updateNotificationStatus('initializing');
    
    // Start system after short delay
    setTimeout(() => {
        console.log('🎯 Starting enhanced notification system...');
        startPolling();
    }, 1000);

    // Log system info
    console.log('🚀 Enhanced Real-time Notification System Initialized');
    console.log('📊 Configuration:', CONFIG);
    console.log('🔧 Debug functions available: window.debugNotification');
    console.log('🎯 Filter jenis pesanan:', jenisPesanan || 'Semua');
    console.log('⏱️ Popup Duration:', CONFIG.POPUP_DURATION + 'ms');
    console.log('🔊 Audio Dturation:', CONFIG.AUDIO_DURATION + 'ms');
    console.log('📝 Known Pesanan IDs:', knownPesananIds.size);
});
</script>
@endpush