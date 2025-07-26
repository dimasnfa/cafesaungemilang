document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function () {
        // Ambil data item-name dari atribut tombol
        const itemName = this.getAttribute('data-item-name');
        
        // Isi input itemName di modal
        document.getElementById('itemName').value = itemName;
    });
});
