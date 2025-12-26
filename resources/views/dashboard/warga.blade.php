<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Warga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h3 class="text-lg font-bold mb-4 px-2">Tagihan Bulanan Saya</h3>

            @if ($tagihans->isEmpty())
                <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">
                    Belum ada tagihan saat ini.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($tagihans as $tagihan)
                        <div
                            class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ $tagihan->status == 'paid' ? 'border-green-500' : 'border-red-500' }}">

                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">Tgl. Cetak Tagihan :</div>
                                    <div class="text-sm font-bold text-gray-800">
                                        {{ $tagihan->created_at->format('d M Y') }}
                                    </div>

                                    <div class="text-gray-400 text-xs font-mono mt-1">
                                        #{{ $tagihan->invoice_code }}
                                    </div>
                                </div>

                                <span
                                    class="px-2 py-1 text-xs font-bold text-white rounded {{ $tagihan->status == 'paid' ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ strtoupper($tagihan->status) }}
                                </span>
                            </div>

                            <div class="mb-4 pt-2 border-t border-gray-100">
                                <h3 class="text-2xl font-bold text-gray-900">Rp
                                    {{ number_format($tagihan->total_amount, 0, ',', '.') }}</h3>
                                <div class="text-gray-600 text-xs mt-2 space-y-1">
                                    <div class="flex justify-between">
                                        <span>Iuran RT:</span>
                                        <span>Rp {{ number_format($tagihan->amount_rt) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Iuran Sampah:</span>
                                        <span>Rp {{ number_format($tagihan->amount_sampah) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Iuran Lingkungan:</span>
                                        <span>Rp {{ number_format($tagihan->amount_lingkungan) }}</span>
                                    </div>
                                </div>
                            </div>

                            @if ($tagihan->status == 'pending')
                                <a href="{{ route('payment.pay', $tagihan->id) }}"
                                    class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    Bayar Sekarang
                                </a>

                                <p class="text-xs text-gray-400 text-center mt-2">
                                    Metode Pembayaran :<br>
                                    QRIS / Virtual Account / E-Wallet
                                </p>
                            @else
                                <button disabled
                                    class="w-full bg-gray-100 text-gray-400 font-bold py-2 px-4 rounded border border-gray-200 cursor-not-allowed">
                                    LUNAS
                                </button>

                                <div class="mt-3">
                                    <p class="text-xs text-green-600 text-center font-semibold">
                                        Terima kasih sudah membayar.
                                    </p>
                                    <p class="text-xs text-green-600 text-center mt-1 font-semibold">
                                        Tanggal Bayar : {{ $tagihan->updated_at->format('d M Y') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
