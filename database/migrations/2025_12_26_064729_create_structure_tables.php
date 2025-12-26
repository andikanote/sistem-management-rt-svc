<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Tabel Users (Warga/Admin)
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('warga'); // admin, sekretaris, warga
            $table->string('no_rumah')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
        });

        // 2. Tabel Periode Tagihan (Agar tidak duplikat bulan)
        Schema::create('invoice_periods', function (Blueprint $table) {
            $table->id();
            $table->string('month_year'); // Contoh: "01-2025"
            $table->date('due_date'); // Jatuh tempo
            $table->timestamps();
        });

        // 3. Tabel Tagihan (Invoices)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('invoice_code')->unique(); // INV-202501-USERID
            $table->decimal('amount_rt', 10, 2); // Iuran RT
            $table->decimal('amount_sampah', 10, 2); // Uang Sampah
            $table->decimal('amount_lingkungan', 10, 2); // Uang Lingkungan
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('snap_token')->nullable(); // Token Midtrans
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_periods');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'no_rumah', 'no_hp', 'alamat']);
        });
    }
};
