// document.addEventListener("DOMContentLoaded", function () {
//     const tableInput = document.getElementById("tableNumber");
//     const posisiTempatSelect = document.getElementById("posisi_tempat");

//     // Data stok dan kapasitas meja
//     const tables = {
//         "Lesehan": { stok: 10, kapasitas: 6, lantai: 1 },
//         "Meja Cafe Lantai 1": { stok: 10, kapasitas: 4, lantai: 1 },
//         "Meja Cafe Lantai 2": { stok: 10, kapasitas: 4, lantai: 2 }
//     };

//     function updateTableInfo(selectedTable) {
//         if (tables[selectedTable]) {
//             tableInput.value = `${selectedTable} (Kapasitas: ${tables[selectedTable].kapasitas}, Stok: ${tables[selectedTable].stok})`;

//             // Update posisi tempat sesuai lantai meja yang dipilih
//             posisiTempatSelect.innerHTML = `<option value="${tables[selectedTable].lantai}">Lantai ${tables[selectedTable].lantai}</option>`;
//         }
//     }

//     // Event listener untuk memilih tipe meja dari modal
//     document.querySelectorAll(".table-option").forEach(option => {
//         option.addEventListener("click", function () {
//             const selectedTable = this.getAttribute("data-table");
//             updateTableInfo(selectedTable);
//         });
//     });
// });
