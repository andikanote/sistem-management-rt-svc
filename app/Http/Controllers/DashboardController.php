<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Invoice; // Import Model Invoice
use App\Models\User;    // Import Model User

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Logic untuk Role Admin atau Sekretaris
        if ($user->role == 'admin' || $user->role == 'sekretaris') {

            // --- LOGIC FILTER PENCARIAN ---
            $query = Invoice::with('user');

            // Filter Search (Nama Warga atau Kode Invoice)
            if ($request->has('search') && $request->search != null) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($u) use ($search) {
                        $u->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhere('invoice_code', 'LIKE', "%$search%");
                });
            }

            // Filter Status (Pending / Paid)
            if ($request->has('status') && $request->status != null) {
                $query->where('status', $request->status);
            }

            // Ambil data dengan Pagination (10 per halaman)
            $tagihan_paginated = $query->latest()->paginate(10)->withQueryString();

            // --- DATA STATISTIK UNTUK ADMIN ---
            $data = [
                // DATA ORANG
                'total_warga' => User::where('role', 'warga')->count(),
                'jumlah_belum_bayar' => Invoice::where('status', 'pending')->count(),
                'jumlah_sudah_bayar' => Invoice::where('status', 'paid')
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count(),

                // DATA UANG
                'total_tagihan_pending' => Invoice::where('status', 'pending')->sum('total_amount'),
                'total_uang_masuk' => Invoice::where('status', 'paid')->sum('total_amount'),

                // List Tagihan untuk Tabel
                'tagihan_terbaru' => $tagihan_paginated
            ];

            return view('dashboard.admin', compact('data'));

        } else {
            // 2. Dashboard Warga (Ditambahkan Fitur Filter)
            $query = Invoice::where('user_id', $user->id);

            // Filter berdasarkan Bulan jika dipilih
            if ($request->filled('month')) {
                $query->whereMonth('created_at', $request->month);
            }

            // Filter berdasarkan Tahun jika dipilih
            if ($request->filled('year')) {
                $query->whereYear('created_at', $request->year);
            }

            // Mengambil data tagihan milik warga tersebut
            $tagihans = $query->orderBy('created_at', 'desc')->get();

            return view('dashboard.warga', compact('tagihans'));
        }
    }
}
