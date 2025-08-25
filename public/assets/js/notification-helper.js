// ===== ✅ CONFIGURATION - Optimized untuk MidtransPooling =====
const CONFIG = {
    POLLING_INTERVAL: 3000,        // 3 detik untuk responsivitas optimal
    POPUP_DURATION: 7000,          // 7 detik untuk popup notification
    AUDIO_DURATION: 16000,         // 16 detik untuk audio
    NOTIFICATION_COOLDOWN: 2000,   // 2 detik cooldown antar notification
    MAX_RETRY_ATTEMPTS: 5,         // Max retry sebelum stop polling
    RETRY_INTERVAL: 5000,          // Base retry interval (5 detik)
    CACHE_BUSTER_LENGTH: 8         // Panjang random string untuk cache buster
};

// ===== ✅ GLOBAL VARIABLES =====
let isPolling = false;
let pollingInterval = null;
let lastNotificationTime = 0;
let retryAttempts = 0;
let knownPesananIds = new Set();
let audioContext = null;
let notificationSound = null;
let jenisPesanan = null; // dinein, takeaway, atau null untuk semua
let autoRefreshIndicator = null;
let csrfToken = null;

// ===== ✅ INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Notification Helper Initialized for MidtransPooling');
    
    // Setup CSRF token
    csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.warn('⚠️ CSRF token tidak ditemukan');
    }

    // Setup auto refresh indicator
    autoRefreshIndicator = document.getElementById('autoRefreshIndicator');
    
    // Detect jenis pesanan dari URL atau data attribute
    detectJenisPesanan();
    
    // Initialize audio context untuk notification sound
    initializeAudioContext();
    
    // Load existing pesanan IDs untuk avoid duplicate notification
    loadExistingPesananIds();
    
    // Auto start polling jika di halaman admin pesanan
    if (window.location.pathname.includes('/admin/pesanan')) {
        setTimeout(startPolling, 1000); // Delay 1 detik untuk stabilitas
    }
    
    console.log('✅ Notification system ready dengan MidtransPooling support');
});

// ===== ✅ DETECT JENIS PESANAN =====
function detectJenisPesanan() {
    // Dari URL
    if (window.location.pathname.includes('/dinein')) {
        jenisPesanan = 'dinein';
    } else if (window.location.pathname.includes('/takeaway')) {
        jenisPesanan = 'takeaway';
    }
    
    // Dari data attribute
    const pageContainer = document.querySelector('[data-jenis-pesanan]');
    if (pageContainer) {
        jenisPesanan = pageContainer.dataset.jenisPesanan;
    }
    
    // Dari query parameter
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('jenis')) {
        jenisPesanan = urlParams.get('jenis');
    }
    
    console.log('🔍 Jenis pesanan detected:', jenisPesanan || 'all');
}

// ===== ✅ AUDIO INITIALIZATION =====
function initializeAudioContext() {
    try {
        // Create audio context untuk notification sound
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
        console.log('🔊 Audio context initialized');
    } catch (error) {
        console.warn('⚠️ Audio context tidak didukung:', error.message);
    }
}

// ===== ✅ NOTIFICATION SOUND =====
function playNotificationSound(duration = CONFIG.AUDIO_DURATION) {
    if (!audioContext) {
        console.warn('⚠️ Audio context tidak tersedia');
        return;
    }

    try {
        // Generate notification beep dengan oscillator
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.2);
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
        
        console.log('🔊 Notification sound played for', duration + 'ms');
    } catch (error) {
        console.warn('⚠️ Error playing notification sound:', error.message);
    }
}

// ===== ✅ LOAD EXISTING PESANAN IDS =====
function loadExistingPesananIds() {
    // Ambil ID pesanan yang sudah ada dari tabel untuk avoid duplicate notification
    const existingRows = document.querySelectorAll('tr[data-pesanan-id]');
    existingRows.forEach(row => {
        const pesananId = parseInt(row.dataset.pesananId);
        if (pesananId) {
            knownPesananIds.add(pesananId);
        }
    });
    
    console.log('📋 Loaded existing pesanan IDs:', Array.from(knownPesananIds));
}

// ===== ✅ POLLING CONTROL FUNCTIONS =====
function startPolling() {
    if (isPolling) {
        console.log('⏸️ Polling already running');
        return;
    }

    isPolling = true;
    retryAttempts = 0;
    console.log('▶️ Starting MidtransPooling notification system');
    
    updateNotificationStatus('active', '🔄 Monitoring QRIS & Cash');
    
    // Immediate first check
    checkForNewOrders();
    
    // Setup interval polling
    pollingInterval = setInterval(checkForNewOrders, CONFIG.POLLING_INTERVAL);
}

function stopPolling() {
    isPolling = false;
    
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
    
    updateNotificationStatus('inactive', '⏹️ Monitoring Stopped');
    console.log('⏹️ MidtransPooling notification stopped');
}

function togglePolling() {
    if (isPolling) {
        stopPolling();
    } else {
        startPolling();
    }
}

// ===== ✅ STATUS UPDATE FUNCTION =====
function updateNotificationStatus(status, message = '') {
    const statusBadge = document.querySelector('.notif-status .badge');
    
    if (!statusBadge) {
        console.warn('⚠️ Status badge element tidak ditemukan');
        return;
    }

    // Reset classes
    statusBadge.className = 'badge';
    
    // Apply status-specific styling
    switch (status) {
        case 'active':
            statusBadge.classList.add('badge-success');
            statusBadge.textContent = message || '✅ MidtransPooling Active';
            break;
        case 'checking':
            statusBadge.classList.add('badge-info');
            statusBadge.textContent = '🔍 Checking QRIS/Cash...';
            break;
        case 'new_order':
            statusBadge.classList.add('badge-warning');
            statusBadge.textContent = `🔔 New Order! ${message}`;
            break;
        case 'error':
            statusBadge.classList.add('badge-danger');
            statusBadge.textContent = `❌ ${message}`;
            break;
        case 'inactive':
            statusBadge.classList.add('badge-secondary');
            statusBadge.textContent = message || '⏸️ Inactive';
            break;
        default:
            statusBadge.classList.add('badge-secondary');
            statusBadge.textContent = message || '⏸️ Unknown';
    }
}

// ===== ✅ ENHANCED NOTIFICATION DISPLAY sesuai dengan MidtransPooling =====
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
        // Normalisasi jenis pesanan
        const jenisPesanan = pesananData.jenis_pesanan?.toLowerCase().replace(/[-\s]/g, '');
        
        console.log('🔍 Jenis pesanan detected:', jenisPesanan, 'from:', pesananData.jenis_pesanan);
        
        // Tentukan metode pembayaran untuk display
        const metodePembayaran = pesananData.metode_pembayaran || pesananData.pembayaran || 'Unknown';
        const isQris = metodePembayaran.toLowerCase() === 'qris';
        const paymentIcon = isQris ? '📱 QRIS' : '💵 CASH';
        
        if (jenisPesanan === 'dinein') {
            titleText = `🍽️ Pesanan Dine-In Baru! ${paymentIcon}`;
            htmlContent = `
                <div class="notification-popup-content">
                    <div class="order-details-box">
                        <div class="detail-row">
                            <strong>Nomor Meja</strong> : <span>${pesananData.nomor_meja || '01'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Tipe Meja</strong> : <span>${pesananData.tipe_meja || 'Regular'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Lantai</strong> : <span>${pesananData.lantai || '1'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Pembayaran</strong> : <span>${paymentIcon}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Status Bayar</strong> : <span class="badge badge-${isQris ? 'success' : 'warning'}">${isQris ? 'LUNAS' : 'PENDING'}</span>
                        </div>
                    </div>
                    <div class="total-amount">
                        Total : <strong>Rp ${new Intl.NumberFormat('id-ID').format(pesananData.total_harga || 0)}</strong>
                    </div>
                </div>
            `;
        } else if (jenisPesanan === 'takeaway') {
            titleText = `🥡 Pesanan Takeaway Baru! ${paymentIcon}`;
            htmlContent = `
                <div class="notification-popup-content">
                    <div class="order-details-box">
                        <div class="detail-row">
                            <strong>Nama Pelanggan</strong> : <span>${pesananData.nama_pelanggan || 'Customer'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Nomor WA</strong> : <span>${pesananData.nomor_wa || '081234567890'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Pembayaran</strong> : <span>${paymentIcon}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Status Bayar</strong> : <span class="badge badge-${isQris ? 'success' : 'warning'}">${isQris ? 'LUNAS' : 'PENDING'}</span>
                        </div>
                    </div>
                    <div class="total-amount">
                        Total : <strong>Rp ${new Intl.NumberFormat('id-ID').format(pesananData.total_harga || 0)}</strong>
                    </div>
                </div>
            `;
        } else {
            // Fallback untuk jenis pesanan tidak dikenal
            titleText = `📥 Pesanan Baru Masuk! ${paymentIcon}`;
            htmlContent = `
                <div class="notification-popup-content">
                    <div class="order-details-box">
                        <div class="detail-row">
                            <strong>Jenis Pesanan</strong> : <span>${pesananData.jenis_pesanan || 'Unknown'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Pembayaran</strong> : <span>${paymentIcon}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Status Pesanan</strong> : <span class="badge badge-info">${pesananData.status_pesanan || 'PENDING'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>Status Bayar</strong> : <span class="badge badge-${isQris ? 'success' : 'warning'}">${isQris ? 'LUNAS' : 'PENDING'}</span>
                        </div>
                        <div class="detail-row">
                            <strong>ID Pesanan</strong> : <span>#${pesananData.id || '000'}</span>
                        </div>
                    </div>
                    <div class="total-amount">
                        Total : <strong>Rp ${new Intl.NumberFormat('id-ID').format(pesananData.total_harga || 0)}</strong>
                    </div>
                </div>
            `;
        }
    } else {
        // Handle case when pesananData is null/undefined
        titleText = '📥 Pesanan Baru Masuk!';
        htmlContent = `
            <div class="notification-popup-content">
                <div class="order-details-box">
                    <div class="detail-row">
                        <strong>Status</strong> : <span>Pesanan baru terdeteksi</span>
                    </div>
                    <div class="detail-row">
                        <strong>Waktu</strong> : <span>${new Date().toLocaleTimeString('id-ID')}</span>
                    </div>
                    <div class="detail-row">
                        <strong>Via</strong> : <span>MidtransPooling</span>
                    </div>
                </div>
                <div class="total-amount">
                    <strong>Silakan cek detail pesanan</strong>
                </div>
            </div>
        `;
    }

    // Debug log
    console.log('🔔 Showing MidtransPooling notification:', {
        title: titleText,
        jenisPesanan: pesananData?.jenis_pesanan,
        metodePembayaran: pesananData?.metode_pembayaran,
        processedJenis: pesananData?.jenis_pesanan?.toLowerCase().replace(/[-\s]/g, ''),
        data: pesananData
    });

    // Play sound IMMEDIATELY ketika notifikasi muncul dengan durasi 16 detik
    playNotificationSound(CONFIG.AUDIO_DURATION);

    // Show SweetAlert notification dengan desain sesuai gambar (7 detik)
    Swal.fire({
        title: titleText,
        html: htmlContent,
        icon: iconType,
        confirmButtonText: '👀 Lihat Pesanan',
        confirmButtonColor: '#28a745',
        cancelButtonText: 'Tutup',
        showCancelButton: true,
        timer: CONFIG.POPUP_DURATION,
        timerProgressBar: true,
        allowOutsideClick: false,
        allowEscapeKey: true,
        width: 450,
        customClass: {
            popup: 'custom-notification-popup midtrans-pooling-popup',
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
            rgba(40, 167, 69, 0.4)
            left top
            no-repeat
        `
    }).then((result) => {
        if (result.isConfirmed) {
            // Refresh halaman untuk menampilkan pesanan terbaru
            window.location.reload();
        }
    });

    console.log('🔔 MidtransPooling notification displayed for:', pesananData);
}

// ===== ✅ ENHANCED MAIN POLLING FUNCTION untuk MidtransPooling =====
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
    if (jenisPesanan) {
        params.append('jenis', jenisPesanan);
    }
    params.append('_t', Date.now()); // Cache buster
    params.append('_r', Math.random().toString(36).substring(7)); // Additional randomness
    params.append('pooling', '1'); // Marker untuk MidtransPooling
    
    const url = `${baseUrl}?${params.toString()}`;

    console.log('🔍 MidtransPooling checking for new orders:', url);

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Cache-Control': 'no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache',
            'Expires': '0',
            'X-CSRF-TOKEN': csrfToken?.content || '',
            'X-MidtransPooling': '1' // Special header untuk pooling
        },
        cache: 'no-store',
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('📡 MidtransPooling response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📋 MidtransPooling response data:', data);
        retryAttempts = 0; // Reset retry counter on success
        
        if (data.new_pesanan && data.pesanan_data) {
            console.log('🔔 NEW ORDER DETECTED via MidtransPooling!', data.pesanan_data);
            
            // Check if this is truly a new pesanan
            if (!knownPesananIds.has(data.pesanan_data.id)) {
                // Update status to show new order
                const metodePembayaran = data.pesanan_data.metode_pembayaran || 'Unknown';
                const paymentIcon = metodePembayaran.toLowerCase() === 'qris' ? '📱' : '💵';
                updateNotificationStatus('new_order', `${paymentIcon} ID: ${data.pesanan_data.id}`);
                
                // Show notification immediately dengan data lengkap
                showNotification(data.pesanan_data);
                
                // Add new row to table
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
            updateNotificationStatus('active', '✅ MidtransPooling Normal');
            
            // Hide auto-refresh indicator
            if (autoRefreshIndicator) {
                autoRefreshIndicator.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('❌ MidtransPooling error:', error);
        retryAttempts++;
        
        updateNotificationStatus('error', `Connection Error (${retryAttempts}/${CONFIG.MAX_RETRY_ATTEMPTS})`);
        
        // Hide auto-refresh indicator on error
        if (autoRefreshIndicator) {
            autoRefreshIndicator.style.display = 'none';
        }
        
        // Stop polling if max retries reached
        if (retryAttempts >= CONFIG.MAX_RETRY_ATTEMPTS) {
            console.error('❌ Max retry attempts reached, stopping MidtransPooling');
            stopPolling();
            updateNotificationStatus('error', '❌ MidtransPooling Lost');
        } else {
            // Retry after delay dengan exponential backoff
            const retryDelay = CONFIG.RETRY_INTERVAL * Math.pow(2, retryAttempts - 1);
            setTimeout(() => {
                if (isPolling) {
                    updateNotificationStatus('active', '↻ Reconnecting MidtransPooling');
                }
            }, retryDelay);
        }
    });
}

// ===== ✅ ADD NEW ROW TO TABLE =====
function addNewRowToTable(pesananData) {
    const tableBody = document.querySelector('#pesananTable tbody, .table tbody');
    
    if (!tableBody) {
        console.warn('⚠️ Table body tidak ditemukan untuk add new row');
        return;
    }

    try {
        // Mark this pesanan as known
        knownPesananIds.add(pesananData.id);
        
        // Determine payment method display
        const metodePembayaran = pesananData.metode_pembayaran || 'Unknown';
        const isQris = metodePembayaran.toLowerCase() === 'qris';
        const paymentBadgeClass = isQris ? 'success' : 'warning';
        const paymentText = isQris ? 'QRIS' : 'CASH';
        
        // Create new row HTML
        const newRow = document.createElement('tr');
        newRow.classList.add('new-pesanan');
        newRow.setAttribute('data-pesanan-id', pesananData.id);
        
        // Format tanggal dan waktu
        const tanggal = pesananData.tanggal_pesanan || new Date().toISOString().split('T')[0];
        const waktu = pesananData.waktu_pesanan || new Date().toLocaleTimeString('id-ID');
        
        newRow.innerHTML = `
            <td class="text-center">
                <span class="badge badge-info">#${pesananData.id}</span>
            </td>
            <td>
                ${pesananData.jenis_pesanan === 'dinein' ? 
                    `<strong>Meja ${pesananData.nomor_meja || '01'}</strong>` : 
                    `<strong>${pesananData.nama_pelanggan || 'Customer'}</strong><br><small class="text-muted">${pesananData.nomor_wa || '-'}</small>`
                }
            </td>
            <td>
                <span class="badge badge-${paymentBadgeClass}">${paymentText}</span>
                ${isQris ? '<small class="text-success d-block">✅ LUNAS</small>' : '<small class="text-warning d-block">⏳ PENDING</small>'}
            </td>
            <td class="text-right">
                <strong>Rp ${new Intl.NumberFormat('id-ID').format(pesananData.total_harga || 0)}</strong>
            </td>
            <td>
                <span class="badge badge-warning">PENDING</span>
            </td>
            <td class="text-center">
                <small>${tanggal}<br>${waktu}</small>
            </td>
            <td class="text-center">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-success btn-sm" onclick="confirmPesanan(${pesananData.id})" title="Konfirmasi Pesanan">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="viewDetail(${pesananData.id})" title="Lihat Detail">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </td>
        `;
        
        // Prepend ke awal table (pesanan terbaru di atas)
        tableBody.insertBefore(newRow, tableBody.firstChild);
        
        console.log('✅ New row added to table via MidtransPooling:', pesananData.id);
        
    } catch (error) {
        console.error('❌ Error adding new row to table:', error);
    }
}

// ===== ✅ UTILITY FUNCTIONS =====
function refreshPage() {
    console.log('🔄 Manual page refresh triggered');
    window.location.reload();
}

function clearNotifications() {
    knownPesananIds.clear();
    loadExistingPesananIds();
    updateNotificationStatus('active', '🧹 Notifications Cleared');
    console.log('🧹 Notification history cleared');
}

// ===== ✅ EXPOSE GLOBAL FUNCTIONS =====
window.startPolling = startPolling;
window.stopPolling = stopPolling;
window.togglePolling = togglePolling;
window.refreshPage = refreshPage;
window.clearNotifications = clearNotificatcleions;