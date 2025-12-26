<?php

namespace App\Http\Controllers;

use App\Models\Invoice; // Pastikan Model Invoice di-import
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Menghapus tagihan berdasarkan ID.
     */
    public function destroy($id)
    {
        // Cari tagihan berdasarkan ID, jika tidak ketemu akan error 404 otomatis
        $invoice = Invoice::findOrFail($id);

        // Hapus data
        $invoice->delete();

        // Kembali ke halaman admin dengan pesan sukses
        return back()->with('success', 'Tagihan berhasil dihapus.');
    }

    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Update status jadi paid
        $invoice->update([
            'status' => 'paid',
            // Opsional: Jika kamu punya kolom payment_method, bisa diisi 'manual'
            // 'payment_method' => 'manual'
        ]);

        return back()->with('success', 'Tagihan berhasil ditandai LUNAS (Pembayaran Manual).');
    }
}
