$(document).ready(function () {
    // Memuat session data saat dokumen siap.
    // Jika elemen tidak ada, val() akan mengembalikan undefined, yang akan divalidasi.
    const jenisPesanan = $('#session-jenis').val();
    const mejaId = $('#session-meja').val();
    const nomorWa = $('#session-wa').val();
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Debug log untuk memastikan session data tersedia.
    // Kondisi ini memastikan log hanya muncul jika data ada, menghindari log yang tidak perlu.
    if (jenisPesanan || mejaId || nomorWa) {
        console.log('Session Data:', {
            jenisPesanan: jenisPesanan,
            mejaId: mejaId,
            nomorWa: nomorWa
        });
    }

    /**
     * @description Memformat angka menjadi format Rupiah.
     * @param {number|string} amount - Angka yang akan diformat.
     * @returns {string} - Teks Rupiah yang sudah diformat.
     */
    function formatRupiah(amount) {
        const parsedAmount = parseInt(amount);
        if (isNaN(parsedAmount)) {
            console.error('Invalid amount provided to formatRupiah:', amount);
            return 'Rp 0';
        }
        return 'Rp ' + parsedAmount.toLocaleString('id-ID');
    }

    /**
     * @description Mengupdate badge jumlah item di keranjang.
     * @param {number} count - Jumlah item di keranjang.
     */
    function updateCartBadge(count) {
        const cartBadge = $('#cart-count');
        if (cartBadge.length) {
            cartBadge.text(count || 0);
            if (count > 0) {
                cartBadge.removeClass('d-none').addClass('badge bg-primary');
            } else {
                cartBadge.addClass('d-none');
            }
        }
    }

    /**
     * @description Mengambil data keranjang dari localStorage.
     * @returns {Array} - Array of cart items.
     */
    function getCartFromLocalStorage() {
        try {
            const cartData = localStorage.getItem('cart');
            return cartData ? JSON.parse(cartData) : [];
        } catch (e) {
            console.warn('Error parsing cart from localStorage:', e);
            return [];
        }
    }

    /**
     * @description Menyimpan data keranjang ke localStorage.
     * @param {Array} cartData - Data keranjang yang akan disimpan.
     */
    function syncCartToLocalStorage(cartData) {
        try {
            if (cartData && Array.isArray(cartData)) {
                localStorage.setItem('cart', JSON.stringify(cartData));
            }
        } catch (e) {
            console.warn('Error saving cart to localStorage:', e);
        }
    }

    /**
     * @description Mendapatkan nama folder gambar berdasarkan kategori menu.
     * @param {string} kategori - Kategori menu.
     * @returns {string} - Nama folder.
     */
    function getFolderFromCategory(kategori) {
        const folderMap = {
            'Makanan': 'makanan',
            'Minuman': 'minuman',
            'Nasi dan Mie': 'nasi-dan-mie',
            'Aneka Snack': 'aneka-snack'
        };
        return folderMap[kategori] || 'default';
    }

    /**
     * @description Menampilkan popup rekomendasi dengan data dari server.
     * @param {number} menuId - ID menu yang baru saja ditambahkan.
     */
    function showRecommendationPopup(menuId) {
        const popup = $('#recommendationPopup');
        const popupBody = $('#recommendationBody');

        if (!popup.length || !popupBody.length) {
            console.log('Recommendation popup elements not found, skipping recommendation');
            return;
        }

        console.log('Showing recommendation popup for menu:', menuId);
        
        // Menampilkan popup dengan status loading.
        popup.show();
        popupBody.html(`
            <div class="recommendation-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Mencari rekomendasi terbaik untuk Anda...</p>
            </div>
        `);
        
        const currentCart = getCartFromLocalStorage();
        const selectedMenus = currentCart.map(item => item.id);
        
        // Menambahkan ID menu yang baru ditambahkan ke daftar.
        if (!selectedMenus.includes(parseInt(menuId))) {
            selectedMenus.push(parseInt(menuId));
        }
        
        console.log('Selected menus for recommendation:', selectedMenus);
        
        const recommendationUrl = $('#recommendation-route').val() || '/dinein/rekomendasi/get';
        
        $.ajax({
            url: recommendationUrl,
            method: 'POST',
            dataType: 'json',
            timeout: 15000, // Meningkatkan timeout menjadi 15 detik
            data: {
                selected_menus: selectedMenus,
                current_menu_id: parseInt(menuId),
                _token: csrfToken
            },
            success: function(response) {
                console.log('Recommendation response:', response);
                
                if (response.status && response.recommendations && response.recommendations.length > 0) {
                    displayRecommendations(response);
                } else {
                    displayNoRecommendations(response.message || 'Belum ada rekomendasi yang cocok saat ini.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching recommendations:', { status, error, responseText: xhr.responseText, statusCode: xhr.status });
                displayErrorRecommendations(JSON.parse(xhr.responseText)?.message || 'Terjadi kesalahan sistem saat memuat rekomendasi.');
            }
        });
    }

    /**
     * @description Menampilkan daftar rekomendasi di popup.
     * @param {object} data - Data rekomendasi dari server.
     */
    function displayRecommendations(data) {
        const popupBody = $('#recommendationBody');
        const algorithmUsed = data.algorithm_used || 'unknown';
        
        let algorithmInfo = `<div class="algorithm-info mt-3"><small><i class="fas fa-lightbulb"></i> Rekomendasi berdasarkan kategori dan popularitas menu</small></div>`;
        if (algorithmUsed === 'apriori') {
            algorithmInfo = `<div class="algorithm-info mt-3"><small><i class="fas fa-brain"></i> Rekomendasi berdasarkan algoritma Apriori - Analisis pola pembelian pelanggan</small></div>`;
        }
        
        let recommendationsHtml = `<h4 class="mb-3">Mungkin Anda Suka?</h4><div class="recommendation-items d-flex flex-wrap justify-content-around">`;
        
        data.recommendations.forEach(function(item) {
            const folder = getFolderFromCategory(item.kategori);
            const imagePath = `/assets/img/${folder}/${item.gambar || 'default.png'}`;
            const formattedPrice = new Intl.NumberFormat('id-ID').format(item.harga);
            
            recommendationsHtml += `
                <div class="recommendation-item card text-center mb-3 mx-2" style="width: 18rem;" data-id="${item.id}">
                    <img src="${imagePath}" class="card-img-top img-fluid" alt="${item.nama_menu}" onerror="this.src='/assets/img/default.png'">
                    <div class="card-body">
                        <h5 class="card-title">${item.nama_menu}</h5>
                        <p class="card-text price fw-bold">Rp. ${formattedPrice}</p>
                        ${item.confidence > 0 ? `<span class="confidence badge bg-info text-dark mb-2">${item.confidence}% confidence</span>` : ''}
                        <p class="rule-text text-muted mb-2"><small>${item.rule_text || 'Rekomendasi untuk Anda'}</small></p>
                        <button class="btn btn-primary recommendation-add-btn w-100" data-id="${item.id}" data-name="${item.nama_menu}" data-price="${item.harga}">
                            <i class="fas fa-plus"></i> Tambah
                        </button>
                    </div>
                </div>
            `;
        });
        
        recommendationsHtml += `</div>${algorithmInfo}`;
        popupBody.html(recommendationsHtml);
        
        bindRecommendationEvents();
    }

    /**
     * @description Menampilkan pesan jika tidak ada rekomendasi.
     * @param {string} message - Pesan yang akan ditampilkan.
     */
    function displayNoRecommendations(message) {
        const popupBody = $('#recommendationBody');
        popupBody.html(`
            <div class="text-center p-4">
                <i class="fas fa-utensils" style="font-size: 48px; color: #bdc3c7; margin-bottom: 20px;"></i>
                <h4 class="text-muted mb-2">Belum Ada Rekomendasi</h4>
                <p class="text-secondary">${message}</p>
            </div>
        `);
    }

    /**
     * @description Menampilkan pesan error saat memuat rekomendasi.
     * @param {string} message - Pesan error yang akan ditampilkan.
     */
    function displayErrorRecommendations(message) {
        const popupBody = $('#recommendationBody');
        popupBody.html(`
            <div class="text-center p-4">
                <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #e74c3c; margin-bottom: 20px;"></i>
                <h4 class="text-danger mb-2">Terjadi Kesalahan</h4>
                <p class="text-secondary">${message}</p>
            </div>
        `);
    }

    /**
     * @description Menutup popup rekomendasi.
     */
    function closeRecommendationPopup() {
        $('#recommendationPopup').hide();
    }
    
    // Bind events untuk menutup popup rekomendasi.
    $(document).on('click', '.close-recommendation', closeRecommendationPopup);
    $(document).on('click', '#recommendationPopup', function(e) {
        if (e.target === this) {
            closeRecommendationPopup();
        }
    });
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRecommendationPopup();
        }
    });

    /**
     * @description Menambah item rekomendasi ke keranjang.
     * @param {number} menuId - ID menu yang direkomendasikan.
     * @param {string} menuName - Nama menu yang direkomendasikan.
     * @param {number} menuPrice - Harga menu yang direkomendasikan.
     */
    function addRecommendedToCart(menuId, menuName, menuPrice) {
        // ... (Logika validasi yang sama seperti add to cart utama) ...
        const validationResult = validateSession();
        if (!validationResult.valid) {
            showValidationWarning(validationResult.text);
            return;
        }

        const postData = {
            _token: csrfToken,
            menu_id: menuId,
            jenis_pesanan: jenisPesanan,
            qty: 1
        };
        if (jenisPesanan === 'dinein') {
            postData.meja_id = mejaId;
        }
        
        const requestUrl = jenisPesanan === 'dinein' ? '/dinein/store' : '/takeaway/store';

        const buttonElement = $(`.recommendation-add-btn[data-id="${menuId}"]`);
        const originalButtonHtml = buttonElement.html();
        buttonElement.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: requestUrl,
            method: 'POST',
            dataType: 'json',
            data: postData,
            success: function (response) {
                handleCartResponse(response, menuName);
                if (response.success) {
                    closeRecommendationPopup();
                }
            },
            error: function (xhr) {
                handleCartError(xhr, 'Gagal menambahkan item rekomendasi ke keranjang.');
            },
            complete: function() {
                buttonElement.prop('disabled', false).html(originalButtonHtml);
            }
        });
    }

    /**
     * @description Menangani respons sukses dari server setelah menambah/mengupdate keranjang.
     * @param {object} response - Objek respons dari server.
     * @param {string|null} menuName - Nama menu yang ditambahkan (opsional).
     */
    function handleCartResponse(response, menuName = null) {
        if (response.success) {
            updateUI(response);
            if (menuName) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `${menuName} berhasil ditambahkan ke keranjang.`,
                    timer: 1500,
                    showConfirmButton: false,
                    ...swalConfig
                });
            }
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Gagal',
                text: response.message || 'Item tidak dapat ditambahkan.',
                ...swalConfig
            });
        }
    }

    /**
     * @description Menangani error dari permintaan AJAX ke server.
     * @param {object} xhr - Objek XMLHttpRequest.
     * @param {string} defaultMessage - Pesan default jika tidak ada pesan dari server.
     */
    function handleCartError(xhr, defaultMessage) {
        console.error('AJAX Error:', xhr);
        let errorMessage = defaultMessage;
        try {
            const errorResponse = JSON.parse(xhr.responseText);
            if (errorResponse.message) {
                errorMessage = errorResponse.message;
            }
        } catch (e) {}
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: errorMessage,
            ...swalConfig
        });
    }

    /**
     * @description Memperbarui seluruh elemen UI terkait keranjang.
     * @param {object} response - Objek respons dari server.
     */
    function updateUI(response) {
        if (response.cart_html) {
            $('#cart-items').html(response.cart_html);
        }
        if (response.order_summary) {
            $('#order-summary').html(response.order_summary);
        }
        if (response.cart_count !== undefined) {
            updateCartBadge(response.cart_count);
        }
        if (response.total !== undefined && $('#totalPrice').length) {
            $('#totalPrice').text(formatRupiah(response.total));
        }
        if (response.cart_data) {
            syncCartToLocalStorage(response.cart_data);
        }
    }
    
    // Konfigurasi SweetAlert yang umum
    const swalConfig = {
        width: '40rem',
        padding: '2rem',
        customClass: { popup: 'custom-swal-popup' }
    };

    /**
     * @description Melakukan validasi data sesi.
     * @returns {object} - Objek dengan properti `valid` (boolean) dan `text` (string).
     */
    function validateSession() {
        if (!jenisPesanan) {
            return { valid: false, text: 'Jenis pesanan belum dipilih. Silakan pilih Dine-In atau Takeaway.' };
        }
        if (jenisPesanan === 'dinein' && !mejaId) {
            return { valid: false, text: 'ID Meja belum dipilih. Silakan lengkapi data booking.' };
        }
        if (jenisPesanan === 'takeaway' && !nomorWa) {
            return { valid: false, text: 'Nomor WhatsApp belum diisi. Silakan lengkapi data pelanggan terlebih dahulu.' };
        }
        return { valid: true, text: '' };
    }

    /**
     * @description Menampilkan SweetAlert warning.
     * @param {string} text - Pesan warning.
     */
    function showValidationWarning(text) {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: text,
            ...swalConfig
        });
    }

    // Bind event untuk tombol "Tambah ke Keranjang" di menu utama.
    $(document).on('click', '.add-to-cart-btn', function (e) {
        e.preventDefault();

        const menuId = $(this).data('id');
        const menuName = $(this).data('name');
        
        const validationResult = validateSession();
        if (!validationResult.valid) {
            showValidationWarning(validationResult.text);
            return;
        }

        const postData = {
            _token: csrfToken,
            menu_id: menuId,
            jenis_pesanan: jenisPesanan,
            qty: 1
        };
        if (jenisPesanan === 'dinein') {
            postData.meja_id = mejaId;
        }

        const requestUrl = jenisPesanan === 'dinein' ? '/dinein/store' : '/takeaway/store';
        const buttonElement = $(this);
        buttonElement.prop('disabled', true).text('Menambah...');

        $.ajax({
            url: requestUrl,
            method: 'POST',
            dataType: 'json',
            data: postData,
            success: function (response) {
                console.log('Add to cart response:', response);
                handleCartResponse(response, menuName);
                if (response.success) {
                    setTimeout(() => {
                        showRecommendationPopup(menuId);
                    }, 300);
                }
            },
            error: function (xhr) {
                handleCartError(xhr, 'Gagal menambahkan item ke keranjang.');
            },
            complete: function() {
                buttonElement.prop('disabled', false).text('Tambah ke Keranjang');
            }
        });
    });

    // Bind event untuk tombol update quantity.
    $(document).on('click', '.btn-qty, .update-qty', function (e) {
        e.preventDefault();
        
        const cartId = $(this).data('cart-id') || $(this).data('id');
        const action = $(this).data('action');

        const validationResult = validateSession();
        if (!validationResult.valid) {
            showValidationWarning(validationResult.text);
            return;
        }

        if (!cartId || !['increase', 'decrease'].includes(action)) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Data tidak valid.', ...swalConfig });
            return;
        }

        $(this).prop('disabled', true);
        const updateUrl = jenisPesanan === 'dinein' ? '/dinein/update' : '/takeaway/update';

        $.ajax({
            url: updateUrl,
            method: 'POST',
            dataType: 'json',
            data: { cart_id: cartId, action: action, _token: csrfToken },
            success: function (response) {
                if (response.success) {
                    updateUI(response);
                    // Tidak perlu notifikasi sukses, perubahan di UI sudah cukup.
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: response.message || 'Tidak bisa memperbarui jumlah item.',
                        ...swalConfig
                    });
                }
            },
            error: function (xhr) {
                handleCartError(xhr, 'Gagal memperbarui item.');
            },
            complete: function() {
                $('.btn-qty, .update-qty').prop('disabled', false);
            }
        });
    });

    // Bind event untuk tombol hapus item.
    $(document).on('click', '.btn-delete-cart, .delete-cart', function (e) {
        e.preventDefault();
        
        const cartId = $(this).data('cart-id') || $(this).data('id');
        
        const validationResult = validateSession();
        if (!validationResult.valid) {
            showValidationWarning(validationResult.text);
            return;
        }
        
        if (!cartId) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Cart ID tidak ditemukan.', ...swalConfig });
            return;
        }

        const destroyUrl = jenisPesanan === 'dinein' ? `/dinein/destroy/${cartId}` : `/takeaway/destroy/${cartId}`;

        Swal.fire({
            title: 'Yakin ingin menghapus item ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            ...swalConfig
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus item...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: destroyUrl,
                    method: 'DELETE',
                    dataType: 'json',
                    data: { _token: csrfToken },
                    success: function (response) {
                        if (response.success) {
                            updateUI(response);
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus!',
                                text: 'Item berhasil dihapus dari keranjang.',
                                timer: 2000,
                                showConfirmButton: false,
                                ...swalConfig
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal',
                                text: response.message || 'Gagal menghapus item.',
                                ...swalConfig
                            });
                        }
                    },
                    error: function (xhr) {
                        handleCartError(xhr, 'Gagal menghapus item dari keranjang.');
                    }
                });
            }
        });
    });

    /**
     * @description Bind events untuk tombol di dalam popup rekomendasi.
     */
    function bindRecommendationEvents() {
        $('.recommendation-add-btn').off('click').on('click', function(e) {
            e.preventDefault();
            const menuId = $(this).data('id');
            const menuName = $(this).data('name');
            const menuPrice = $(this).data('price');
            addRecommendedToCart(menuId, menuName, menuPrice);
        });
    }

    // Fungsi ini dipanggil saat DOM siap untuk memastikan badge sudah terupdate.
    // Ini penting jika ada item di localStorage saat halaman dimuat ulang.
    (function initializeCartUI() {
        const cart = getCartFromLocalStorage();
        updateCartBadge(cart.length);
    })();
});