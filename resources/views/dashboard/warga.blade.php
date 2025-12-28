<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Warga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-2 mb-6">
                <h3 class="text-lg font-bold text-gray-700">
                    Hi {{ auth()->user()->name }},
                </h3>
                <p class="text-sm text-gray-500">
                    Berikut tagihan bulanan dan tagihan lain-nya, Segera dibayarkan sebelum jatuh tempo!
                </p>
            </div>

            @if ($tagihans->isEmpty())
                <div class="bg-white p-10 rounded-xl shadow-sm text-center">
                    <div class="flex justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada tagihan saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($tagihans as $tagihan)
                        <div
                            class="bg-white overflow-hidden shadow-sm rounded-xl p-6 border-t-4 {{ $tagihan->status == 'paid' ? 'border-green-500' : 'border-red-500' }} transition-transform hover:scale-[1.02] duration-300">

                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <div class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Invoice
                                    </div>
                                    <div class="text-sm font-mono font-bold text-gray-600">#{{ $tagihan->invoice_code }}
                                    </div>
                                </div>
                                <span
                                    class="px-3 py-1 text-xs font-bold rounded-full {{ $tagihan->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ strtoupper($tagihan->status) }}
                                </span>
                            </div>

                            <div class="mb-6">
                                <div class="text-xs text-gray-500 mb-1">Periode:
                                    {{ $tagihan->created_at->format('M Y') }}</div>
                                <h3 class="text-3xl font-extrabold text-gray-900">
                                    Rp {{ number_format($tagihan->total_amount, 0, ',', '.') }}
                                </h3>

                                <div class="mt-4 p-3 bg-gray-50 rounded-lg space-y-2">
                                    <div class="flex justify-between text-xs text-gray-600">
                                        <span>Iuran RT</span>
                                        <span class="font-semibold text-gray-800">Rp
                                            {{ number_format($tagihan->amount_rt ?? 0, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="flex justify-between text-xs text-gray-600 relative group">
                                        <span class="cursor-help border-b border-dotted border-gray-400">Sampah &
                                            Lingkungan</span>
                                        <span class="font-semibold text-gray-800">
                                            Rp
                                            {{ number_format(($tagihan->amount_sampah ?? 0) + ($tagihan->amount_lingkungan ?? 0), 0, ',', '.') }}
                                        </span>

                                        <div
                                            class="absolute bottom-full left-0 mb-2 hidden group-hover:block w-48 p-3 bg-gray-800 text-white text-[10px] rounded-lg shadow-xl z-30 transition-opacity duration-300">
                                            <div class="flex justify-between mb-1">
                                                <span class="opacity-80">Iuran Sampah:</span>
                                                <span class="font-bold text-emerald-400">Rp
                                                    {{ number_format($tagihan->amount_sampah ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="opacity-80">Iuran Lingkungan:</span>
                                                <span class="font-bold text-emerald-400">Rp
                                                    {{ number_format($tagihan->amount_lingkungan ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div
                                                class="absolute top-full left-5 w-2 h-2 bg-gray-800 transform rotate-45 -mt-1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($tagihan->status == 'pending')
                                <button type="button" onclick="openPayment({{ $tagihan->id }})"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-colors duration-200 flex items-center justify-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <span>Bayar Sekarang</span>
                                </button>
                                <p
                                    class="text-[10px] text-gray-400 text-center mt-3 uppercase tracking-widest font-bold font-mono">
                                    QRIS / VA / E-WALLET / ETC.
                                </p>
                            @else
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                                    <p class="text-sm text-green-700 font-bold flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        LUNAS
                                    </p>
                                    <p class="text-[10px] text-green-600 mt-1 opacity-75">
                                        Dibayar:
                                        {{ $tagihan->updated_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity"></div>

            <div
                class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl sm:w-full">
                <div class="flex justify-between items-center p-4 border-b bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800">Pilih Metode Pembayaran</h3>
                    <button type="button" onclick="closePaymentModal()"
                        class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="relative w-full bg-white" style="height: 650px;">
                    <div id="loadingPayment"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-white z-10">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                        <p class="mt-4 text-gray-500 font-medium">Menghubungkan ke Gateway Pembayaran...</p>
                    </div>
                    <iframe id="xenditIframe" src="" class="w-full h-full border-none"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPayment(invoiceId) {
            const modal = document.getElementById('paymentModal');
            const iframe = document.getElementById('xenditIframe');
            const loader = document.getElementById('loadingPayment');

            iframe.src = "";
            loader.style.display = 'flex';
            modal.classList.remove('hidden');

            fetch(`/payment/${invoiceId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.checkout_url) {
                        iframe.src = data.checkout_url;
                        iframe.onload = () => {
                            loader.style.display = 'none';
                        };
                    } else {
                        alert(data.error || 'Gagal memproses pembayaran.');
                        closePaymentModal();
                    }
                })
                .catch(() => {
                    alert('Terjadi kesalahan koneksi.');
                    closePaymentModal();
                });
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('xenditIframe').src = "";
            // window.location.reload(); // Aktifkan jika ingin refresh otomatis saat modal tutup
        }
    </script>
</x-app-layout>
