<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon; // Tambahkan ini untuk pengolahan tanggal

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role == 'admin' || $user->role == 'sekretaris') {
            $data = [
                // 1. Total Warga
                'total_warga' => \App\Models\User::where('role', 'warga')->count(),

                // 2. Total Uang Pending (Rupiah)
                'total_tagihan_pending' => \App\Models\Invoice::where('status', 'pending')->sum('total_amount'),

                // 3. Jumlah Orang Belum Bayar (Count)
                'jumlah_belum_bayar' => \App\Models\Invoice::where('status', 'pending')->count(),

                // 4. Jumlah Orang Sudah Bayar Bulan Ini (Count)
                'jumlah_sudah_bayar' => \App\Models\Invoice::where('status', 'paid')
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count(),

                // List 5 Tagihan Terakhir
                'tagihan_terbaru' => \App\Models\Invoice::with('user')->latest()->take(5)->get()
            ];

            // Pastikan nama view ini sesuai dengan lokasi file blade kamu
            // Jika file kamu bernama admin.blade.php di folder views, ubah jadi return view('admin', ...);
            // Sesuai kode awal kamu: return view('dashboard.admin', ...);
            return view('dashboard.admin', compact('data'));

        } else {
            // Dashboard Warga
            $tagihans = \App\Models\Invoice::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')->get();
            return view('dashboard.warga', compact('tagihans'));
        }
    }
}
