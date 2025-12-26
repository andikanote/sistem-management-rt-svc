<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        Configuration::setXenditKey(env('XENDIT_API_KEY'));
    }

    public function pay(Invoice $invoice)
    {
        // 1. Validasi Pemilik
        if ($invoice->user_id != auth()->id()) {
            abort(403);
        }

        // 2. Jika sudah lunas, jangan bayar lagi
        if ($invoice->status == 'paid') {
            return redirect()->route('dashboard')->with('error', 'Tagihan sudah lunas!');
        }

        // 3. Jika Link Pembayaran sudah ada, langsung redirect saja (biar gak bikin invoice dobel di Xendit)
        if (!empty($invoice->checkout_link)) {
            return redirect($invoice->checkout_link);
        }

        // 4. Buat Invoice Baru ke Xendit
        $apiInstance = new InvoiceApi();

        // Parameter Invoice
        $create_invoice_request = new \Xendit\Invoice\CreateInvoiceRequest([
            'external_id' => (string) $invoice->invoice_code,
            'description' => 'Pembayaran Iuran RT Bulan ' . $invoice->created_at->format('F Y'),
            'amount' => $invoice->total_amount,
            'invoice_duration' => 86400, // Link berlaku 24 jam
            'currency' => 'IDR',
            'customer' => [
                'given_names' => auth()->user()->name,
                'email' => auth()->user()->email,
                'mobile_number' => auth()->user()->no_hp ?? '-',
            ],
            // Redirect user kembali ke dashboard setelah bayar
            'success_redirect_url' => route('dashboard'),
            'failure_redirect_url' => route('dashboard'),
        ]);

        try {
            $result = $apiInstance->createInvoice($create_invoice_request);

            // 5. Simpan Link Pembayaran ke Database
            $invoice->update([
                'checkout_link' => $result['invoice_url'],
                'external_id'   => $result['id']
            ]);

            // 6. Redirect User ke Halaman Pembayaran Xendit
            return redirect($result['invoice_url']);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage());
        }
    }

    // WEBHOOK: Menerima notifikasi otomatis dari Xendit
    public function callback(Request $request)
{
    // 1. LOG DATA MASUK (Cek storage/logs/laravel.log nanti)
    Log::info('Xendit Webhook Masuk:', $request->all());

    $xenditXCallbackToken = $request->header('x-callback-token');

    // 2. Cek Token
    if ($xenditXCallbackToken != env('XENDIT_CALLBACK_TOKEN')) {
        Log::error('Token Salah! Masuk: ' . $xenditXCallbackToken . ' | Harusnya: ' . env('XENDIT_CALLBACK_TOKEN'));
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $data = $request->all();

    // 3. Proses Status
    if (isset($data['status']) && $data['status'] == 'PAID') {
        $external_id = $data['external_id'];

        $invoice = Invoice::where('invoice_code', $external_id)->first();

        if ($invoice) {
            $invoice->update(['status' => 'paid']);
            Log::info("Invoice $external_id BERHASIL diupdate jadi PAID");
        } else {
            Log::error("Invoice $external_id TIDAK DITEMUKAN di database");
        }
    }

    return response()->json(['message' => 'Success'], 200);
}
}
