<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('invoices', function (Blueprint $table) {
        // Hapus kolom midtrans
        $table->dropColumn('snap_token');

        // Tambah kolom link pembayaran Xendit
        $table->string('checkout_link')->nullable()->after('status');
        $table->string('external_id')->nullable()->after('id'); // ID unik di Xendit
    });
}

public function down(): void
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->string('snap_token')->nullable();
        $table->dropColumn(['checkout_link', 'external_id']);
    });
}
};
