<nav class="navbar navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <!-- Logo -->
        <a href="/" class="navbar-brand d-flex align-items-center">
            <img src="{{ asset('assets/img/banner/icon-gemilang.png') }}" alt="Gemilang Logo" class="me-2" style="height: 70px;">
            <h1 class="text-primary fw-bold mb-0">
                <span class="d-block">Gemilang</span>
                <span class="text-dark d-block" style="font-size: 0.8em;">Cafe & Saung</span>
            </h1>
        </a>

        <!-- Navbar Links langsung ditampilkan tanpa collapse -->
        <ul class="navbar-nav flex-row justify-content-center align-items-center gap-3 mb-0">
            @if(session('meja') && Request::is('booking/*'))
                <li class="nav-item">
                    <a href="{{ url('/booking/' . session('meja')) }}" class="nav-link {{ Request::is('booking/*') ? 'active' : '' }}">
                        Meja {{ session('meja') }}
                    </a>
                </li>
            @endif
        </ul>
        
        <!-- Bagian Kanan Navbar (Keranjang) -->
        <div class="d-flex align-items-center">
            @if(!Request::is('/'))
                @if(session('meja_id') && !Request::is('takeaway/*'))
                    <!-- Keranjang Dine-In -->
                    <a href="{{ route('cart.dinein.cart') }}" class="position-relative me-3">
                        <i class="fa fa-shopping-cart fa-2x text-primary cart-icon"></i>
                        <span id="cart-count-dinein" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ session('cart_count_dinein', 0) }}
                        </span>
                    </a>
                @elseif(Request::is('takeaway/*'))
                    <!-- Keranjang Takeaway -->
                    <a href="{{ route('cart.takeaway.cart') }}" class="position-relative me-3">
                        <i class="fa fa-shopping-cart fa-2x text-primary cart-icon"></i>
                        <span id="cart-count-takeaway" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ session('cart_count_takeaway', 0) }}
                        </span>
                    </a>
                @endif
            @endif
        </div>
    </div>
</nav>

<!-- CSS untuk animasi cart badge -->
<style>
.cart-badge-animate {
    animation: cartPulse 0.6s ease-in-out;
}

@keyframes cartPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}

.cart-icon-bounce {
    animation: cartBounce 0.5s ease-in-out;
}

@keyframes cartBounce {
    0%, 20%, 60%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    80% { transform: translateY(-5px); }
}

/* ✅ Style untuk cart icon yang mendapat focus saat update */
.cart-icon-highlight {
    color: #28a745 !important;
    animation: iconHighlight 1s ease-in-out;
}

@keyframes iconHighlight {
    0%, 100% { color: #007bff !important; }
    50% { color: #28a745 !important; }
}
</style>

<script>
// ✅ JavaScript untuk auto-refresh cart count yang ditingkatkan
document.addEventListener("DOMContentLoaded", function () {
    
    // ✅ Function untuk update cart count dine-in dengan animasi yang ditingkatkan
    function updateDineinCartCount() {
        const cartCountElement = document.getElementById('cart-count-dinein');
        if (cartCountElement) {
            fetch('/api/cart/dinein/count', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.cart_count !== undefined) {
                    const currentCount = parseInt(cartCountElement.textContent);
                    const newCount = parseInt(data.cart_count);
                    
                    if (currentCount !== newCount) {
                        cartCountElement.textContent = newCount;
                        
                        // ✅ Enhanced animation untuk badge dan icon
                        cartCountElement.classList.add('cart-badge-animate');
                        const cartIcon = cartCountElement.parentElement.querySelector('.cart-icon');
                        if (cartIcon) {
                            cartIcon.classList.add('cart-icon-bounce', 'cart-icon-highlight');
                        }
                        
                        setTimeout(() => {
                            cartCountElement.classList.remove('cart-badge-animate');
                            if (cartIcon) {
                                cartIcon.classList.remove('cart-icon-bounce', 'cart-icon-highlight');
                            }
                        }, 1000);
                    }
                }
            })
            .catch(error => console.log('Error updating dinein cart count:', error));
        }
    }

    // Function untuk update cart count takeaway
    function updateTakeawayCartCount() {
        const cartCountElement = document.getElementById('cart-count-takeaway');
        if (cartCountElement) {
            fetch('/api/cart/takeaway/count', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.cart_count !== undefined) {
                    const currentCount = parseInt(cartCountElement.textContent);
                    const newCount = parseInt(data.cart_count);
                    
                    if (currentCount !== newCount) {
                        cartCountElement.textContent = newCount;
                        // Animate badge jika ada perubahan
                        cartCountElement.classList.add('cart-badge-animate');
                        const cartIcon = cartCountElement.parentElement.querySelector('.cart-icon');
                        if (cartIcon) {
                            cartIcon.classList.add('cart-icon-bounce', 'cart-icon-highlight');
                        }
                        
                        setTimeout(() => {
                            cartCountElement.classList.remove('cart-badge-animate');
                            if (cartIcon) {
                                cartIcon.classList.remove('cart-icon-bounce', 'cart-icon-highlight');
                            }
                        }, 1000);
                    }
                }
            })
            .catch(error => console.log('Error updating takeaway cart count:', error));
        }
    }

    // ✅ Function untuk handle add to cart dengan AJAX yang ditingkatkan
    function handleAddToCart() {
        // Handle semua form add to cart
        const addToCartForms = document.querySelectorAll('.add-to-cart-form');
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        
        // Handle form submission
        addToCartForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                
                // Disable button dan show loading
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menambahkan...';
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // ✅ Update cart count dengan animasi yang ditingkatkan
                        if (data.cart_count !== undefined) {
                            const isDinein = window.location.pathname.includes('dinein') || document.getElementById('cart-count-dinein');
                            const cartCountElement = isDinein ? 
                                document.getElementById('cart-count-dinein') : 
                                document.getElementById('cart-count-takeaway');
                            
                            if (cartCountElement) {
                                cartCountElement.textContent = data.cart_count;
                                // ✅ Enhanced animate cart icon dan badge
                                cartCountElement.classList.add('cart-badge-animate');
                                const cartIcon = cartCountElement.parentElement.querySelector('.cart-icon');
                                if (cartIcon) {
                                    cartIcon.classList.add('cart-icon-bounce', 'cart-icon-highlight');
                                }
                                
                                setTimeout(() => {
                                    cartCountElement.classList.remove('cart-badge-animate');
                                    if (cartIcon) {
                                        cartIcon.classList.remove('cart-icon-bounce', 'cart-icon-highlight');
                                    }
                                }, 1000);
                            }
                        }
                        
                        // Show success message
                        showToast(data.message || 'Item berhasil ditambahkan ke keranjang', 'success');
                    } else {
                        showToast(data.message || 'Gagal menambahkan item ke keranjang', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat menambahkan item', 'error');
                })
                .finally(() => {
                    // Re-enable button
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                });
            });
        });

        // Handle direct button clicks (non-form)
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const menuId = this.dataset.menuId;
                const jenisPesanan = this.dataset.jenisPesanan || 'dinein';
                const qty = this.dataset.qty || 1;
                
                if (!menuId) return;
                
                const originalText = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menambahkan...';
                
                const formData = new FormData();
                formData.append('menu_id', menuId);
                formData.append('qty', qty);
                formData.append('jenis_pesanan', jenisPesanan);
                
                fetch('/add-to-cart', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // ✅ Update cart count dengan animasi enhanced
                        if (data.cart_count !== undefined) {
                            const isDinein = jenisPesanan === 'dinein';
                            const cartCountElement = isDinein ? 
                                document.getElementById('cart-count-dinein') : 
                                document.getElementById('cart-count-takeaway');
                            
                            if (cartCountElement) {
                                cartCountElement.textContent = data.cart_count;
                                // ✅ Enhanced animate cart icon dan badge
                                cartCountElement.classList.add('cart-badge-animate');
                                const cartIcon = cartCountElement.parentElement.querySelector('.cart-icon');
                                if (cartIcon) {
                                    cartIcon.classList.add('cart-icon-bounce', 'cart-icon-highlight');
                                }
                                
                                setTimeout(() => {
                                    cartCountElement.classList.remove('cart-badge-animate');
                                    if (cartIcon) {
                                        cartIcon.classList.remove('cart-icon-bounce', 'cart-icon-highlight');
                                    }
                                }, 1000);
                            }
                        }
                        
                        showToast(data.message || 'Item berhasil ditambahkan ke keranjang', 'success');
                    } else {
                        showToast(data.message || 'Gagal menambahkan item ke keranjang', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat menambahkan item', 'error');
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
            });
        });
    }

    // Function untuk show toast notification
    function showToast(message, type = 'info') {
        // Create toast element if not exists
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();
        
        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    // Initialize cart handling
    handleAddToCart();

    // ✅ Auto-refresh cart count setiap 15 detik (lebih sering untuk responsifitas lebih baik)
    setInterval(() => {
        if (document.getElementById('cart-count-dinein')) {
            updateDineinCartCount();
        }
        if (document.getElementById('cart-count-takeaway')) {
            updateTakeawayCartCount();
        }
    }, 15000); // Dari 30 detik menjadi 15 detik

    // ✅ Refresh cart count saat halaman mendapat focus kembali (lebih cepat)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            setTimeout(() => {
                if (document.getElementById('cart-count-dinein')) {
                    updateDineinCartCount();
                }
                if (document.getElementById('cart-count-takeaway')) {
                    updateTakeawayCartCount();
                }
            }, 500); // Dari 1000ms menjadi 500ms
        }
    });

    // ✅ Tambahkan refresh saat window focus (untuk multi-tab)
    window.addEventListener('focus', () => {
        setTimeout(() => {
            if (document.getElementById('cart-count-dinein')) {
                updateDineinCartCount();
            }
            if (document.getElementById('cart-count-takeaway')) {
                updateTakeawayCartCount();
            }
        }, 300);
    });
});

// ✅ Enhanced global function untuk manual refresh cart count
window.refreshCartCount = function() {
    if (document.getElementById('cart-count-dinein')) {
        fetch('/api/cart/dinein/count', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.cart_count !== undefined) {
                const cartCountElement = document.getElementById('cart-count-dinein');
                cartCountElement.textContent = data.cart_count;
                
                // Animate update
                cartCountElement.classList.add('cart-badge-animate');
                const cartIcon = cartCountElement.parentElement.querySelector('.cart-icon');
                if (cartIcon) {
                    cartIcon.classList.add('cart-icon-highlight');
                }
                
                setTimeout(() => {
                    cartCountElement.classList.remove('cart-badge-animate');
                    if (cartIcon) {
                        cartIcon.classList.remove('cart-icon-highlight');
                    }
                }, 600);
            }
        });
    }
    
    if (document.getElementById('cart-count-takeaway')) {
        fetch('/api/cart/takeaway/count', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.cart_count !== undefined) {
                const cartCountElement = document.getElementById('cart-count-takeaway');
                cartCountElement.textContent = data.cart_count;
                
                // Animate update
                cartCountElement.classList.add('cart-badge-animate');
                const cartIcon = cartCountElement.parentElement.querySelector('.cart-icon');
                if (cartIcon) {
                    cartIcon.classList.add('cart-icon-highlight');
                }
                
                setTimeout(() => {
                    cartCountElement.classList.remove('cart-badge-animate');
                    if (cartIcon) {
                        cartIcon.classList.remove('cart-icon-highlight');
                    }
                }, 600);
            }
        });
    }
};
</script>

@if(Request::is('takeaway*'))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const checkoutButton = document.querySelector("#checkoutButton");
            const takeawayForm = document.querySelector("#takeawayForm");

            if (checkoutButton) {
                checkoutButton.addEventListener("click", function (event) {
                    event.preventDefault();
                    let takeawayModal = new bootstrap.Modal(document.getElementById('takeawayModal'));
                    takeawayModal.show();
                });
            }

            if (takeawayForm) {
                takeawayForm.addEventListener("submit", function (event) {
                    event.preventDefault();
                    let namaPelanggan = document.getElementById("namaPelanggan").value;
                    let nomorWA = document.getElementById("nomorWA").value;

                    if (!namaPelanggan || !nomorWA) {
                        alert("Harap isi Nama Pelanggan dan Nomor WhatsApp sebelum melanjutkan pembayaran.");
                        return;
                    }

                    sessionStorage.setItem("namaPelanggan", namaPelanggan);
                    sessionStorage.setItem("nomorWA", nomorWA);

                    window.location.href = "{{ route('cart.takeaway.cart') }}";
                });
            }
        });
    </script>
@endif