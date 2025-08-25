{{-- Komponen Rekomendasi Menu
<!-- CSS untuk Popup Rekomendasi -->
<style>
/* CSS untuk Popup Rekomendasi */
.recommendation-popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    animation: fadeIn 0.3s ease-in-out;
}

.recommendation-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 15px;
    padding: 30px;
    max-width: 800px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    animation: slideIn 0.3s ease-in-out;
}

.recommendation-header {
    text-align: center;
    margin-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 15px;
}

.recommendation-header h3 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-weight: bold;
}

.recommendation-header p {
    color: #7f8c8d;
    margin: 0;
    font-style: italic;
}

.recommendation-items {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    margin-bottom: 25px;
}

.recommendation-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    flex: 1;
    min-width: 200px;
    max-width: 250px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    cursor: pointer;
}

.recommendation-item:hover {
    border-color: #3498db;
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.2);
}

.recommendation-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
    border: 3px solid #ecf0f1;
}

.recommendation-item h5 {
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: bold;
}

.recommendation-item .price {
    color: #e74c3c;
    font-weight: bold;
    margin-bottom: 8px;
}

.recommendation-item .confidence {
    background: #3498db;
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    display: inline-block;
    margin-bottom: 5px;
}

.recommendation-item .rule-text {
    font-size: 11px;
    color: #7f8c8d;
    margin-bottom: 10px;
    line-height: 1.3;
}

.recommendation-item .add-btn {
    background: #27ae60;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 12px;
    cursor: pointer;
    transition: background 0.3s ease;
    width: 100%;
}

.recommendation-item .add-btn:hover {
    background: #229954;
}

.recommendation-buttons {
    text-align: center;
    border-top: 2px solid #f0f0f0;
    padding-top: 20px;
}

.close-recommendation {
    background: #95a5a6;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s ease;
}

.close-recommendation:hover {
    background: #7f8c8d;
}

.algorithm-info {
    background: #e8f6f3;
    border-left: 4px solid #1abc9c;
    padding: 10px 15px;
    margin-bottom: 20px;
    border-radius: 0 5px 5px 0;
}

.algorithm-info small {
    color: #16a085;
    font-weight: 500;
}

/* Animation keyframes */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: translate(-50%, -60%);
    }
    to { 
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

/* Loading spinner for recommendations */
.recommendation-loading {
    text-align: center;
    padding: 40px 20px;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive design */
@media (max-width: 768px) {
    .recommendation-content {
        padding: 20px;
        margin: 20px;
        width: calc(100% - 40px);
    }
    
    .recommendation-items {
        flex-direction: column;
        align-items: center;
    }
    
    .recommendation-item {
        max-width: 100%;
        min-width: auto;
    }
}

@media (max-width: 480px) {
    .recommendation-content {
        padding: 15px;
    }
}
</style>

<!-- Popup Rekomendasi Menu -->
<div id="recommendationPopup" class="recommendation-popup">
    <div class="recommendation-content">
        <div class="recommendation-header">
            <h3><i class="fas fa-magic"></i> Rekomendasi Menu Untuk Anda</h3>
            <p>Berdasarkan pilihan menu Anda, kami merekomendasikan:</p>
        </div>
        
        <div id="recommendationBody">
            <!-- Content will be loaded here via AJAX -->
            <div class="recommendation-loading">
                <div class="spinner"></div>
                <p>Mencari rekomendasi terbaik untuk Anda...</p>
            </div>
        </div>
        
        <div class="recommendation-buttons">
            <button type="button" class="close-recommendation" onclick="closeRecommendationPopup()">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>
</div>

<!-- Script untuk Rekomendasi Menu -->
<script>
// Function untuk mendapatkan gambar menu (konsisten dengan booking.blade.php)
function getMenuImage(kategori, namaMenu, gambar = null) {
    const folder = strtolower(str_replace([' ', '&'], '-', kategori));
    const defaultGambar = 'default.png';
    
    const customImages = {
        'Makanan': {
            'Nasi': 'nasi.png',
            'Ayam Goreng': 'ayamgoreng.jpg',
            'Ayam Bakar': 'ayambakar.jpg',
            'Ayam Mentega': 'ayamentega.jpg',
            'Ayam Lada Hitam': 'ayamladahitam.jpg',
            'Ayam Lombok Ijo': 'ayamlombokijo.jpg',
            'Ayam Asam Manis': 'ayamasammanis.jpg',
            'Ayam Saos Padang': 'ayamsaospadang.jpg',
            'Ayam Rica Rica': 'ayamricarica.jpg',
            'Sop Ayam': 'sopayam.jpg',
            'Garang Asem Ayam': 'garangasem.jpg',
            'Sambal Mentah': 'sambalmentah.jpg',
            'Sambal Pecak': 'sambalpecak.jpg',
            'Sambal Terasi': 'sambalterasi.jpg',
            'Sambal Geprek': 'sambalgeprek.jpg',
            'Sambal Bawang': 'sambalbawang.jpg',
            'Sambal Ijo': 'sambalijo.jpg',
            'Sambal Dabu Dabu': 'sambaldabu.jpg',
        },
        'Minuman': {
            'Jus Alpukat': 'alpukat.jpg',
            'Jus Apel': 'apel.jpg',
            'Jus Strawberry': 'strawberry.jpg',
            'Jus Jeruk': 'jeruk.jpg',
            'Jus Tomat': 'tomat.jpg',
            'Jus Mangga': 'mangga.jpg',
            'Jus Melon': 'melon.jpg',
            'Jus Fibar': 'fibar.jpg',
            'Jus Wortel': 'wortel.jpg',
            'Jeruk Panas': 'jerukpanas.jpg',
            'Jeruk Dingin': 'jerukdingin.jpg',
            'Teh Manis Panas': 'tehmanis.jpg',
            'Teh Manis Dingin': 'tehmanis.jpg',
            'Coffe Ekspresso': 'coffekspresso.jpg',
            'Cappucino Ice': 'cappucinoice.jpg',
            'Cappucino Hot': 'cappucinohot.jpg',
            'Cofe Susu Gula Aren': 'coffesusugularen.jpg',
            'Best Latte Ice': 'bestlattehot.jpg',
            'Cofe Latte Ice': 'coffelatteice.jpg',
            'Cofe Latte Hot': 'coffelatehot.jpg',
            'Matcha Ice': 'macthaice.jpg',
            'Matcha Hot': 'matchahot.jpg',
            'Coklat Ice': 'coklatice.jpg',
            'Coklat Hot': 'coklathot.jpg',
            'Red Valvet Ice': 'redvlvt.jpg',
            'Red Valvet Hot': 'redvlvt.jpg',
            'Vakal Peach': 'vakalpeach.jpg',
            'Beauty Peach': 'beautypeach.jpg',
            'Teh Tubruk': 'tehtubruk.jpg',
            'Teh Tubruk Susu': 'tehtubruk2.jpg',
        },
        'Nasi dan Mie': {
            'Mie Goreng': 'miegoreng.png',
            'Indomie Rebus': 'indomierebus.png',
            'Indomie Goreng toping': 'indomiegoreng.png',
            'Nasi Goreng Gemilang': 'nasigorenggemilang.png',
            'Nasi Goreng Seafood': 'nasigorengseafood.png',
            'Nasi Goreng Ayam': 'nasigorengayam.png',
            'Kwetiau Goreng': 'kwetiau.png',
            'Kwetiau Rebus': 'kwetiaurebus.png',
        },
        'Aneka Snack': {
            'French Fries': 'frenchfries.jpg',
            'Keong Racun': 'keongracun.jpg',
            'Kongkou Snack': 'KongkouSnack.jpg',
            'Nugget': 'nugget.jpg',
            'Pisang Bakar': 'pisangbakar.jpg',
            'Roti Bakar': 'rotibakar.png',
            'Roti Bakar Keju Coklat': 'rotibakarkejucoklat.jpg',
            'Sosis Goreng': 'sosisgoreng.jpg',
            'Tahu Tepung': 'tahutepung.jpg',
        },
    };
    
    const finalGambar = gambar || (customImages[kategori] && customImages[kategori][namaMenu]) || defaultGambar;
    return `{{ asset('assets/img') }}/${folder}/${finalGambar}`;
}

// Helper functions untuk konsistensi dengan PHP
function strtolower(str) {
    return str.toLowerCase();
}

function str_replace(search, replace, subject) {
    if (Array.isArray(search) && Array.isArray(replace)) {
        for (let i = 0; i < search.length; i++) {
            subject = subject.split(search[i]).join(replace[i]);
        }
        return subject;
    }
    return subject.split(search).join(replace);
}

// Function untuk mendapatkan folder berdasarkan kategori
function getFolderFromCategory(kategori) {
    return strtolower(str_replace([' ', '&'], '-', kategori));
}

// Function untuk menampilkan popup rekomendasi
function showRecommendationPopup(currentMenuId) {
    const popup = document.getElementById('recommendationPopup');
    const body = document.getElementById('recommendationBody');
    
    // Reset content
    body.innerHTML = `
        <div class="recommendation-loading">
            <div class="spinner"></div>
            <p>Mencari rekomendasi terbaik untuk Anda...</p>
        </div>
    `;
    
    // Show popup
    popup.style.display = 'block';
    
    // Get current cart items
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const selectedMenus = cart.map(item => item.id);
    
    // AJAX request untuk mendapatkan rekomendasi
    fetch('{{ route("rekomendasi.get") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            selected_menus: selectedMenus,
            current_menu_id: currentMenuId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status && data.recommendations && data.recommendations.length > 0) {
            displayRecommendations(data);
        } else {
            displayNoRecommendations();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        displayErrorRecommendations();
    });
}

// Function untuk menampilkan rekomendasi
function displayRecommendations(data) {
    const body = document.getElementById('recommendationBody');
    const algorithmUsed = data.algorithm_used || 'unknown';
    
    let algorithmInfo = '';
    if (algorithmUsed === 'apriori') {
        algorithmInfo = `
            <div class="algorithm-info">
                <small><i class="fas fa-brain"></i> Rekomendasi berdasarkan algoritma Apriori - Analisis pola pembelian pelanggan</small>
            </div>
        `;
    } else {
        algorithmInfo = `
            <div class="algorithm-info">
                <small><i class="fas fa-lightbulb"></i> Rekomendasi berdasarkan kategori dan popularitas menu</small>
            </div>
        `;
    }
    
    let recommendationsHtml = algorithmInfo + '<div class="recommendation-items">';
    
    data.recommendations.forEach(function(item) {
        const imagePath = getMenuImage(item.kategori, item.nama_menu, item.gambar);
        
        recommendationsHtml += `
            <div class="recommendation-item" data-id="${item.id}">
                <img src="${imagePath}" alt="${item.nama_menu}" 
                     onerror="this.src='{{ asset('assets/img/default.png') }}'">
                <h5>${item.nama_menu}</h5>
                <p class="price">Rp. ${new Intl.NumberFormat('id-ID').format(item.harga)}</p>
                ${item.confidence > 0 ? `<span class="confidence">${item.confidence}% confidence</span>` : ''}
                <p class="rule-text">${item.rule_text}</p>
                <button class="add-btn" onclick="addRecommendedToCart(${item.id}, '${item.nama_menu}', ${item.harga})">
                    <i class="fas fa-plus"></i> Tambah ke Keranjang
                </button>
            </div>
        `;
    });
    
    recommendationsHtml += '</div>';
    body.innerHTML = recommendationsHtml;
}

// Function untuk menampilkan ketika tidak ada rekomendasi
function displayNoRecommendations() {
    const body = document.getElementById('recommendationBody');
    body.innerHTML = `
        <div style="text-align: center; padding: 40px 20px;">
            <i class="fas fa-utensils" style="font-size: 48px; color: #bdc3c7; margin-bottom: 20px;"></i>
            <h4 style="color: #7f8c8d; margin-bottom: 10px;">Belum Ada Rekomendasi</h4>
            <p style="color: #95a5a6;">Silakan tambah lebih banyak menu untuk mendapatkan rekomendasi yang lebih baik.</p>
        </div>
    `;
}

// Function untuk menampilkan error
function displayErrorRecommendations() {
    const body = document.getElementById('recommendationBody');
    body.innerHTML =
}

// Function untuk menutup popup
function closeRecommendationPopup() {
    document.getElementById('recommendationPopup').style.display = 'none';
}

// Function untuk menambahkan rekomendasi ke keranjang
function addRecommendedToCart(menuId, menuName, menuPrice) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check if item already in cart
    const existingItem = cart.find(item => item.id == menuId);
    
    if (existingItem) {
        existingItem.quantity += 1;
        existingItem.subtotal = existingItem.quantity * existingItem.price;
    } else {
        cart.push({
            id: menuId,
            name: menuName,
            price: menuPrice,
            quantity: 1,
            subtotal: menuPrice
        });
    }
    
    // Update localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Show success message
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: `${menuName} telah ditambahkan ke keranjang`,
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false
    });
    
    // Update cart display if function exists
    if (typeof updateCartDisplay === 'function') {
        updateCartDisplay();
    }
    
    // Close popup
    closeRecommendationPopup();
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Close popup when clicking outside
    const popup = document.getElementById('recommendationPopup');
    if (popup) {
        popup.addEventListener('click', function(e) {
            if (e.target === this) {
                closeRecommendationPopup();
            }
        });
    }
    
    // Close popup with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRecommendationPopup();
        }
    });
});

// Expose functions globally
window.showRecommendationPopup = showRecommendationPopup;
window.closeRecommendationPopup = closeRecommendationPopup;
window.addRecommendedToCart = addRecommendedToCart;
</script> --}}