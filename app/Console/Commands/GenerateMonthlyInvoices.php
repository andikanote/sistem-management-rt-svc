<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Invoice;
use Carbon\Carbon;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoice:generate-monthly';
    protected $description = 'Generate tagihan bulanan untuk semua warga aktif';

    public function handle()
    {
        $this->info('Memulai proses generate tagihan...');

        // Harga Iuran
        $biaya_rt = 50000;
        $biaya_sampah = 20000;
        $biaya_lingkungan = 10000;
        $total = $biaya_rt + $biaya_sampah + $biaya_lingkungan;

        // Format Bulan: 122025 (mY)
        $bulan_ini = Carbon::now()->format('mY');

        // Ambil semua user dengan role warga
        $wargas = User::where('role', 'warga')->get();

        foreach ($wargas as $warga) {
            // Cek apakah sudah ada tagihan bulan ini
            // Pattern: INV + 122025 + ...
            $cek = Invoice::where('user_id', $warga->id)
                ->where('invoice_code', 'LIKE', "INV{$bulan_ini}%")
                ->exists();

            if (!$cek) {

                // GENERATE KODE UNIK 6 DIGIT
                do {
                    $randomSixDigit = mt_rand(100000, 999999);
                    // Format Gabung: INV122025573821
                    $newInvoiceCode = 'INV' . $bulan_ini . $randomSixDigit;
                } while (Invoice::where('invoice_code', $newInvoiceCode)->exists());

                Invoice::create([
                    'user_id' => $warga->id,
                    'invoice_code' => $newInvoiceCode,
                    'amount_rt' => $biaya_rt,
                    'amount_sampah' => $biaya_sampah,
                    'amount_lingkungan' => $biaya_lingkungan,
                    'total_amount' => $total,
                    'status' => 'pending',
                ]);

                $this->info("Tagihan dibuat: " . $warga->name . " | Code: " . $newInvoiceCode);
            }
        }

        $this->info('Selesai generate tagihan bulan ' . $bulan_ini);
    }
}
