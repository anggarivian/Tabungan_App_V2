@extends('layout.main')

@section('content')
<div class="container text-center">
    <h2>Pembayaran Stor Tabungan</h2>
    <p>Jumlah: Rp. {{ number_format($transaksi->jumlah_transaksi, 0, ',', '.') }}</p>
    <button id="pay-button" class="btn btn-success">Bayar Sekarang</button>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('pay-button').onclick = function () {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                console.log("Pembayaran Berhasil!", result);
                window.location.href = "{{ route('siswa.dashboard') }}";
            },
            onPending: function(result) {
                console.log("Menunggu Pembayaran...", result);
            },
            onError: function(result) {
                console.log("Pembayaran Gagal!", result);
            },
            onClose: function() {
                console.log("Transaksi Dibatalkan!");
            }
        });
    };
</script>
@endsection
