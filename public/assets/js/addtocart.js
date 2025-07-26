$(document).ready(function () {
    const jenisPesanan = $('#session-jenis').val();
    const mejaId = $('#session-meja').val();
    const nomorWa = $('#session-wa').val();

    // Debug log untuk memastikan session data tersedia
    console.log('Session Data:', {
        jenisPesanan: jenisPesanan,
        mejaId: mejaId,
        nomorWa: nomorWa
    });

    // ✅ ADDED: Function untuk format rupiah
    function formatRupiah(amount) {
        return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    }

    // ✅ ADDED: Function untuk update cart count badge
    function updateCartBadge(count) {
        const cartBadge = $('#cart-count');
        if (cartBadge.length) {
            cartBadge.text(count || 0);
            // Update badge visibility
            if (count > 0) {
                cartBadge.removeClass('d-none').addClass('badge bg-primary');
            } else {
                cartBadge.addClass('d-none');
            }
        }
    }

    // Tambah item ke keranjang
    $('.add-to-cart-btn').click(function (e) {
        e.preventDefault();

        const menuId = $(this).data('id');

        let valid = true;
        let warningText = '';

        if (!jenisPesanan) {
            valid = false;
            warningText = 'Jenis pesanan belum dipilih. Silakan pilih Dine-In atau Takeaway.';
        } else if (jenisPesanan === 'dinein' && !mejaId) {
            valid = false;
            warningText = 'ID Meja belum dipilih. Silakan lengkapi data booking.';
        } else if (jenisPesanan === 'takeaway' && !nomorWa) {
            valid = false;
            warningText = 'Nomor WhatsApp belum diisi. Silakan lengkapi data pelanggan terlebih dahulu.';
        }

        if (!valid) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Belum Lengkap',
                text: warningText,
                width: '40rem',
                padding: '2rem',
                customClass: { popup: 'custom-swal-popup' }
            });
            return;
        }

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

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

        // Disable button saat proses
        $(this).prop('disabled', true).text('Menambah...');

        $.ajax({
            url: requestUrl,
            method: 'POST',
            dataType: 'json',
            cache: false,
            data: postData,
            success: function (response) {
                if (response.success) {
                    // Update cart display
                    if (response.cart_html) {
                        $('#cart-items').html(response.cart_html);
                    }
                    if (response.order_summary) {
                        $('#order-summary').html(response.order_summary);
                    }
                    
                    // Update cart count
                    updateCartBadge(response.cart_count);
                    
                    // Update total price
                    if (response.total && $('#totalPrice').length) {
                        $('#totalPrice').text(formatRupiah(response.total));
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Item berhasil ditambahkan ke keranjang.',
                        timer: 2000,
                        showConfirmButton: false,
                        width: '40rem',
                        padding: '2rem',
                        customClass: { popup: 'custom-swal-popup' },
                        showClass: { popup: 'animate__animated animate__fadeInDown' },
                        hideClass: { popup: 'animate__animated animate__fadeOutUp' }
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: response.message || 'Item tidak dapat ditambahkan.',
                        width: '40rem',
                        padding: '2rem',
                        customClass: { popup: 'custom-swal-popup' }
                    });
                    console.warn('Server response:', response);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response Text:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal menambahkan item ke keranjang.',
                    width: '40rem',
                    padding: '2rem',
                    customClass: { popup: 'custom-swal-popup' }
                });
            },
            complete: function() {
                // Re-enable button
                $('.add-to-cart-btn').prop('disabled', false).text('Tambah ke Keranjang');
            }
        });
    });

    // ✅ UPDATED: Update Qty - Support multiple class names dan attribute names
    $(document).on('click', '.btn-qty, .update-qty', function (e) {
        e.preventDefault();
        
        // Support multiple data attribute names
        const cartId = $(this).data('cart-id') || $(this).data('id');
        const action = $(this).data('action');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Validation
        if (!cartId) {
            console.error('Cart ID tidak ditemukan');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Cart ID tidak ditemukan.',
                width: '40rem',
                padding: '2rem',
                customClass: { popup: 'custom-swal-popup' }
            });
            return;
        }

        if (!action || !['increase', 'decrease'].includes(action)) {
            console.error('Action tidak valid:', action);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Action tidak valid.',
                width: '40rem',
                padding: '2rem',
                customClass: { popup: 'custom-swal-popup' }
            });
            return;
        }

        // Debug log
        console.log('Update cart:', {
            cartId: cartId,
            action: action,
            jenisPesanan: jenisPesanan
        });

        // Disable button to prevent double clicks
        $(this).prop('disabled', true);

        const updateUrl = jenisPesanan === 'dinein' ? '/dinein/update' : '/takeaway/update';

        $.ajax({
            url: updateUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                cart_id: cartId,
                action: action,
                _token: csrfToken,
            },
            success: function (response) {
                console.log('Update response:', response);
                
                if (response.success) {
                    // Update cart display
                    if (response.cart_html) {
                        $('#cart-items').html(response.cart_html);
                    }
                    if (response.order_summary) {
                        $('#order-summary').html(response.order_summary);
                    }
                    
                    // Update cart count
                    updateCartBadge(response.cart_count);
                    
                    // Update total price
                    if (response.total && $('#totalPrice').length) {
                        $('#totalPrice').text(formatRupiah(response.total));
                    }

                    // ✅ ADDED: Show success notification for better UX
                    if (action === 'increase') {
                        console.log('Item quantity increased successfully');
                    } else {
                        console.log('Item quantity decreased successfully');
                    }
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: response.message || 'Tidak bisa memperbarui jumlah item.',
                        width: '40rem',
                        padding: '2rem',
                        customClass: { popup: 'custom-swal-popup' }
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Update Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                
                let errorMessage = 'Gagal memperbarui item.';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    // If response is not JSON, use default message
                    if (xhr.status === 404) {
                        errorMessage = 'URL tidak ditemukan. Periksa route update cart.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Terjadi kesalahan server. Periksa log server.';
                    } else if (xhr.status === 422) {
                        errorMessage = 'Data tidak valid. Periksa parameter yang dikirim.';
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: errorMessage,
                    width: '40rem',
                    padding: '2rem',
                    customClass: { popup: 'custom-swal-popup' }
                });
            },
            complete: function() {
                // Re-enable all quantity buttons
                $('.btn-qty, .update-qty').prop('disabled', false);
            }
        });
    });

    // ✅ UPDATED: Hapus item - Support multiple class names dan attribute names
    $(document).on('click', '.btn-delete-cart, .delete-cart', function (e) {
        e.preventDefault();
        
        // Support multiple data attribute names
        const cartId = $(this).data('cart-id') || $(this).data('id');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Validation
        if (!cartId) {
            console.error('Cart ID tidak ditemukan');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Cart ID tidak ditemukan.',
                width: '40rem',
                padding: '2rem',
                customClass: { popup: 'custom-swal-popup' }
            });
            return;
        }

        // Debug log
        console.log('Delete cart:', {
            cartId: cartId,
            jenisPesanan: jenisPesanan
        });

        const destroyUrl = jenisPesanan === 'dinein' ? `/dinein/destroy/${cartId}` : `/takeaway/destroy/${cartId}`;

        Swal.fire({
            title: 'Yakin ingin menghapus item ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            width: '40rem',
            padding: '2rem',
            customClass: { popup: 'custom-swal-popup' }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Menghapus item...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: destroyUrl,
                    method: 'DELETE',
                    dataType: 'json',
                    data: { _token: csrfToken },
                    success: function (response) {
                        console.log('Delete response:', response);
                        
                        if (response.success) {
                            // Update cart display
                            if (response.cart_html) {
                                $('#cart-items').html(response.cart_html);
                            }
                            if (response.order_summary) {
                                $('#order-summary').html(response.order_summary);
                            }
                            
                            // Update cart count
                            updateCartBadge(response.cart_count);
                            
                            // Update total price
                            if (response.total !== undefined && $('#totalPrice').length) {
                                $('#totalPrice').text(formatRupiah(response.total));
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus!',
                                text: 'Item berhasil dihapus dari keranjang.',
                                timer: 2000,
                                showConfirmButton: false,
                                width: '40rem',
                                padding: '2rem',
                                customClass: { popup: 'custom-swal-popup' }
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal',
                                text: response.message || 'Gagal menghapus item.',
                                width: '40rem',
                                padding: '2rem',
                                customClass: { popup: 'custom-swal-popup' }
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Delete Error:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText,
                            statusCode: xhr.status
                        });
                        
                        let errorMessage = 'Gagal menghapus item.';
                        
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMessage = errorResponse.message;
                            }
                        } catch (e) {
                            if (xhr.status === 404) {
                                errorMessage = 'URL tidak ditemukan. Periksa route destroy cart.';
                            } else if (xhr.status === 500) {
                                errorMessage = 'Terjadi kesalahan server. Periksa log server.';
                            } else if (xhr.status === 422) {
                                errorMessage = 'Data tidak valid atau item sudah tidak ada.';
                            }
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: errorMessage,
                            width: '40rem',
                            padding: '2rem',
                            customClass: { popup: 'custom-swal-popup' }
                        });
                    }
                });
            }
        });
    });

    // ✅ ADDED: Handle empty cart state
    function checkEmptyCart() {
        const cartItems = $('#cart-items tr');
        if (cartItems.length === 0) {
            $('#cart-items').html(`
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-cart-x fs-1"></i>
                        <div class="mt-2">Keranjang kosong</div>
                        <small>Silakan tambahkan menu untuk melanjutkan</small>
                    </td>
                </tr>
            `);
            
            // Hide payment section
            $('#order-summary').hide();
        }
    }

    // ✅ ADDED: Initialize cart state on page load
    checkEmptyCart();
});