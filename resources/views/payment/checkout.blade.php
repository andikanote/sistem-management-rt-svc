<x-app-layout>
    <div class="py-12 text-center">
        <h2 class="text-xl font-bold">Menyiapkan Pembayaran...</h2>
        <p>Silahkan selesaikan pembayaran melalui popup yang muncul.</p>

        <script type="text/javascript">
            window.snap.pay('{{ $invoice->snap_token }}', {
                onSuccess: function(result){
                    alert("Pembayaran Berhasil!");
                    window.location.href = "{{ route('dashboard') }}";
                },
                onPending: function(result){
                    alert("Menunggu pembayaran!");
                    window.location.href = "{{ route('dashboard') }}";
                },
                onError: function(result){
                    alert("Pembayaran gagal!");
                    window.location.href = "{{ route('dashboard') }}";
                }
            });
        </script>
    </div>
</x-app-layout>
