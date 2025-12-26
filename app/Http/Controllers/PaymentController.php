<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
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

        // 2. Jika sudah lunas
        if ($invoice->status == 'paid') {
            if (request()->ajax()) {
                return response()->json(['error' => 'Tagihan sudah lunas!'], 400);
            }
            return redirect()->route('dashboard')->with('error', 'Tagihan sudah lunas!');
        }

        // 3. Cek apakah Link Pembayaran sudah ada
        $checkoutUrl = $invoice->checkout_link;

        if (empty($checkoutUrl)) {
            // 4. Buat Invoice Baru ke Xendit
            $apiInstance = new InvoiceApi();
            $create_invoice_request = new \Xendit\Invoice\CreateInvoiceRequest([
                'external_id' => (string) $invoice->invoice_code,
                'description' => 'Pembayaran Iuran RT Bulan ' . $invoice->created_at->format('F Y'),
                'amount' => $invoice->total_amount,
                'currency' => 'IDR',
                'customer' => [
                    'given_names' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
                'success_redirect_url' => route('dashboard'),
                'failure_redirect_url' => route('dashboard'),
            ]);

            try {
                $result = $apiInstance->createInvoice($create_invoice_request);
                $checkoutUrl = $result['invoice_url'];

                // Simpan Link ke Database
                $invoice->update([
                    'checkout_link' => $checkoutUrl,
                    'external_id'   => $result['id']
                ]);
            } catch (\Exception $e) {
                if (request()->ajax()) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
                return back()->with('error', 'Gagal membuat pembayaran: ' . $e->getMessage());
            }
        }

        // 5. Response dinamis: JSON untuk AJAX (Popup) atau Redirect untuk akses langsung
        if (request()->ajax()) {
            return response()->json(['checkout_url' => $checkoutUrl]);
        }

        return redirect($checkoutUrl);
    }

    public function callback(Request $request)
    {
        // Logika Webhook tetap sama
        Log::info('Xendit Webhook Masuk:', $request->all());
        $xenditXCallbackToken = $request->header('x-callback-token');

        if ($xenditXCallbackToken != env('XENDIT_CALLBACK_TOKEN')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        if (isset($data['status']) && $data['status'] == 'PAID') {
            $invoice = Invoice::where('invoice_code', $data['external_id'])->first();
            if ($invoice) {
                $invoice->update(['status' => 'paid']);
            }
        }
        return response()->json(['message' => 'Success'], 200);
    }
}
