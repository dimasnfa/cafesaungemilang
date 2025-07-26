{{-- <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    
    <!-- Midtrans Snap.js -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" 
        data-client-key="{{ config('services.midtrans.client_key') }}">
    </script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Checkout</h1>
    <p>Total Bayar: Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
    <button id="pay-button-midtrans">Bayar Sekarang</button>

    <!-- Form untuk mengirimkan data pembayaran -->
    <form id="payment-form" method="POST" action="{{ route('checkout.callback') }}">
        @csrf
        <input type="hidden" name="order_id" id="order_id">
        <input type="hidden" name="transaction_status" id="transaction_status">
        <input type="hidden" name="gross_amount" id="gross_amount">
        <input type="hidden" name="signature_key" id="signature_key">
    </form>

    <script>
        document.getElementById('pay-button-midtrans').onclick = function () {
            snap.pay("{{ $snapToken }}", {
                onSuccess: function(result){
                    sendPaymentData(result);
                },
                onPending: function(result){
                    sendPaymentData(result);
                },
                onError: function(result){
                    alert('Pembayaran gagal');
                    console.log(result);
                },
                onClose: function(){
                    alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                }
            });
        };

        function sendPaymentData(result) {
            $('#order_id').val(result.order_id);
            $('#transaction_status').val(result.transaction_status);
            $('#gross_amount').val(result.gross_amount);
            $('#signature_key').val(result.signature_key);
            $('#payment-form').submit();
        }
    </script>
</body>
</html> --}}
