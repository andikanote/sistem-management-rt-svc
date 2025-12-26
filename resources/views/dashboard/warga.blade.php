<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Warga') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h3 class="text-lg font-bold mb-4 px-2">Tagihan Saya</h3>

            @if ($tagihans->isEmpty())
                <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">
                    Belum ada tagihan saat ini.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($tagihans as $tagihan)
                        <div
                            class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ $tagihan->status == 'paid' ? 'border-green-500' : 'border-red-500' }}">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-gray-500 text-xs font-mono">#{{ $tagihan->invoice_code }}</span>
                                <span
                                    class="px-2 py-1 text-xs font-bold text-white rounded {{ $tagihan->status == 'paid' ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ strtoupper($tagihan->status) }}
                                </span>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-2xl font-bold">Rp
                                    {{ number_format($tagihan->total_amount, 0, ',', '.') }}</h3>
                                <p class="text-gray-600 text-xs mt-1">
                                    RT: {{ number_format($tagihan->amount_rt) }} |
                                    Sampah: {{ number_format($tagihan->amount_sampah) }} |
                                    Lingk: {{ number_format($tagihan->amount_lingkungan) }}
                                </p>
                            </div>

                            @if ($tagihan->status == 'pending')
                                <a href="{{ route('payment.pay', $tagihan->id) }}"
                                    class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    Bayar Sekarang (QRIS / VA / E-Wallet)
                                </a>

                                <p class="text-xs text-gray-500 text-center mt-2">
                                    Anda akan diarahkan ke halaman pembayaran aman Xendit.
                                </p>
                            @else
                                <button disabled
                                    class="w-full bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded cursor-not-allowed">
                                    Lunas
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
