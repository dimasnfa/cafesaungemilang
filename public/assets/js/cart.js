    // $(document).ready(function () {

    //     // ============================
    //     // Update Quantity
    //     // ============================
    //     $(document).on("click", ".update-qty", function () {
    //         const cartId = $(this).data("id");
    //         const action = $(this).data("action");
    //         const token = $('meta[name="csrf-token"]').attr("content");
    //         const jenisPesanan = $(this).data("jenis") || $('#jenis-pesanan-session').data("jenis");

    //         if (!jenisPesanan || !["dinein", "takeaway"].includes(jenisPesanan)) {
    //             return showError("Jenis Pemesanan Tidak Diketahui", "Silakan mulai ulang pemesanan.");
    //         }

    //         const updateUrl = `/cart/${jenisPesanan}/update`;

    //         $.ajax({
    //             url: updateUrl,
    //             method: "POST",
    //             data: {
    //                 _token: token,
    //                 cart_id: cartId,
    //                 action: action
    //             },
    //             success: function (response) {
    //                 if (response.success) {
    //                     $(".cart-count").text(response.cart_count);
    //                     $("#cart-items").html(response.cart_html ?? "");
    //                     $("#order-summary").html(response.order_summary ?? "");
    //                     updateCartTotal();

    //                     if (parseInt(response.cart_count) === 0) location.reload();
    //                 } else {
    //                     showError("Gagal", response.message || "Gagal memperbarui kuantitas.");
    //                 }
    //             },
    //             error: function () {
    //                 showError("Oops!", "Gagal memperbarui item di keranjang.");
    //             }
    //         });
    //     });

    //     // ============================
    //     // Delete Cart Item
    //     // ============================
    //     $(document).on("click", ".delete-cart", function () {
    //         const cartId = $(this).data("id");
    //         const token = $('meta[name="csrf-token"]').attr("content");
    //         const jenisPesanan = $(this).data("jenis") || $('#jenis-pesanan-session').data("jenis");

    //         if (!jenisPesanan || !["dinein", "takeaway"].includes(jenisPesanan)) {
    //             return showError("Jenis Pemesanan Tidak Diketahui", "Silakan mulai ulang pemesanan.");
    //         }

    //         const destroyUrl = `/cart/${jenisPesanan}/destroy/${cartId}`;

    //         Swal.fire({
    //             title: "Apakah Anda yakin?",
    //             text: "Item ini akan dihapus dari keranjang.",
    //             icon: "warning",
    //             showCancelButton: true,
    //             confirmButtonColor: "#d33",
    //             cancelButtonColor: "#3085d6",
    //             confirmButtonText: "Ya, hapus!"
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 $.ajax({
    //                     url: destroyUrl,
    //                     method: "DELETE",
    //                     data: { _token: token },
    //                     success: function (response) {
    //                         if (response.success) {
    //                             $(".cart-count").text(response.cart_count);
    //                             $("#cart-items").html(response.cart_html ?? "");
    //                             $("#order-summary").html(response.order_summary ?? "");
    //                             updateCartTotal();

    //                             Swal.fire({
    //                                 icon: "success",
    //                                 title: "Dihapus!",
    //                                 text: "Item telah dihapus dari keranjang.",
    //                                 timer: 1500,
    //                                 showConfirmButton: false
    //                             });

    //                             if (parseInt(response.cart_count) === 0) location.reload();
    //                         } else {
    //                             showError("Gagal", response.message || "Item tidak dapat dihapus.");
    //                         }
    //                     },
    //                     error: function () {
    //                         showError("Oops!", "Gagal menghapus item dari keranjang.");
    //                     }
    //                 });
    //             }
    //         });
    //     });

    //     // ============================
    //     // Update Total Cart Harga
    //     // ============================
    //     function updateCartTotal() {
    //         let total = 0;
    //         $(".cart-item-price").each(function () {
    //             const priceText = $(this).text().replace(/[^\d]/g, "");
    //             const price = parseInt(priceText) || 0;
    //             total += price;
    //         });
    //         $("#totalPrice").text("Rp " + total.toLocaleString("id-ID"));
    //     }

    //     // ============================
    //     // Form Data Pelanggan Takeaway
    //     // ============================
    //     $("#formCustomerData").on("submit", function (e) {
    //         e.preventDefault();
    //         const formData = $(this).serialize();

    //         $.ajax({
    //             url: "/save-customer-data",
    //             method: "POST",
    //             data: formData,
    //             success: function (res) {
    //                 if (res.status === 'success') {
    //                     if (!res.snap_token) {
    //                         console.error("Snap Token kosong atau tidak valid.");
    //                         return showError("Gagal", "Token pembayaran tidak tersedia.");
    //                     }

    //                     if (typeof snap === "undefined") {
    //                         console.error("Midtrans Snap.js belum dimuat.");
    //                         return showError("Gagal", "Midtrans belum siap. Silakan coba lagi.");
    //                     }

    //                     console.log("Snap Token:", res.snap_token);

    //                     snap.pay(res.snap_token, {
    //                         onSuccess: function(result) {
    //                             Swal.fire('Berhasil', 'Pembayaran berhasil!', 'success');
    //                             console.log("Success:", result);
    //                             window.location.href = "/order/success"; // redirect jika ada halaman sukses
    //                         },
    //                         onPending: function(result) {
    //                             Swal.fire('Menunggu', 'Silakan selesaikan pembayaran.', 'info');
    //                             console.log("Pending:", result);
    //                         },
    //                         onError: function(result) {
    //                             console.error("Midtrans Error:", result);
    //                             Swal.fire('Gagal', 'Terjadi kesalahan saat pembayaran.', 'error');
    //                         },
    //                         onClose: function() {
    //                             Swal.fire('Ditutup', 'Kamu menutup pembayaran sebelum selesai.', 'warning');
    //                         }
    //                     });
    //                 } else {
    //                     showError("Gagal", res.message || "Terjadi kesalahan.");
    //                 }
    //             },
    //             error: function (xhr) {
    //                 const errMsg = xhr.responseJSON?.message || "Terjadi kesalahan saat menyimpan data.";
    //                 showError("Oops!", errMsg);
    //             }
    //         });
    //     });

    //     // ============================
    //     // Helper: Tampilkan Error
    //     // ============================
    //     function showError(title, message) {
    //         Swal.fire({
    //             icon: "error",
    //             title: title,
    //             text: message
    //         });
    //     }

    // });
