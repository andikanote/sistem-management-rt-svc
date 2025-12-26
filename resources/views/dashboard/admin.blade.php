<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Admin') }}
            </h2>

            <form action="{{ route('admin.invoices.generate') }}" method="POST"
                onsubmit="return confirm('Yakin ingin generate tagihan bulan ini manual?');">
                @csrf
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm transition ease-in-out duration-150">
                    ⚡ Generate Tagihan Manual
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                    role="alert">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-medium">Total Warga</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $data['total_warga'] }} Warga</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-500">
                    <div class="text-gray-500 text-sm font-medium">Belum Bayar</div>
                    <div class="text-2xl font-bold text-orange-600">
                        {{ $data['jumlah_belum_bayar'] }} Warga
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium">Lunas Bulan Ini</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ $data['jumlah_sudah_bayar'] }} Warga
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-gray-500 text-sm font-medium">Potensi Uang Masuk</div>
                    <div class="text-2xl font-bold text-red-600 truncate">
                        Rp {{ number_format($data['total_tagihan_pending'], 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-3 md:space-y-0">
                        <h3 class="font-bold text-lg">Daftar Tagihan</h3>

                        <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2 w-full md:w-auto">

                            <select name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">- Semua Status -</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Bayar</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                            </select>

                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari Nama / Invoice..."
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm w-full md:w-64">
                            </div>

                            <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Cari
                            </button>

                            @if(request('search') || request('status'))
                                <a href="{{ route('dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded text-sm text-center">
                                    Reset
                                </a>
                            @endif
                        </form>
                        </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="py-2 px-4 border-b text-left">Invoice</th>
                                    <th class="py-2 px-4 border-b text-left">Nama</th>
                                    <th class="py-2 px-4 border-b text-left">Total Pembayaran</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                    <th class="py-2 px-4 border-b text-left">Tgl. Cetak Tagihan</th>
                                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['tagihan_terbaru'] as $invoice)
                                    <tr>
                                        <td class="py-2 px-4 border-b text-sm font-mono">{{ $invoice->invoice_code }}
                                        </td>
                                        <td class="py-2 px-4 border-b">{{ $invoice->user->name }}</td>
                                        <td class="py-2 px-4 border-b">Rp
                                            {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <span
                                                class="px-2 py-1 text-xs rounded {{ $invoice->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-sm text-gray-500">
                                            {{ $invoice->created_at->format('d M Y') }}</td>

                                        <td class="py-2 px-4 border-b text-center">
                                            <div class="flex item-center justify-center space-x-2">

                                                @if ($invoice->status == 'pending')
                                                    <form action="{{ route('invoices.markPaid', $invoice->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Konfirmasi: Warga ini sudah bayar tunai/manual?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-3 rounded text-xs"
                                                            title="Tandai Lunas">
                                                            Bayar Manual
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button"
                                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs cursor-default"
                                                            title="Sudah Lunas">
                                                            Sudah Lunas
                                                    </button>
                                                @endif

                                                <form action="{{ route('invoices.destroy', $invoice->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus tagihan {{ $invoice->invoice_code }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs"
                                                        title="Hapus Tagihan">
                                                        Hapus
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-gray-500">
                                            Data tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $data['tagihan_terbaru']->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
