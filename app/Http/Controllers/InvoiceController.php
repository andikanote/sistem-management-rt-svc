<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Menghapus tagihan berdasarkan ID.
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return back()->with('success', 'Tagihan berhasil dihapus.');
    }

    /**
     * Update status jadi PAID (Manual) + Nama Warga di notifikasi
     */
    public function markAsPaid($id)
    {
        // 1. Cari data invoice
        $invoice = Invoice::findOrFail($id);

        // 2. Ambil nama warga dari relasi user
        // Pastikan Model Invoice punya function user() { return $this->belongsTo(User::class); }
        $namaWarga = $invoice->user->name;

        // 3. Update status jadi paid
        $invoice->update([
            'status' => 'paid',
        ]);

        // 4. Tampilkan pesan dengan nama warga
        // Gunakan tanda kutip dua (") agar variabel terbaca
        return back()->with('success', "Sukses! Tagihan $namaWarga berhasil ditandai LUNAS (Pembayaran Manual).");
    }
}
