<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            //
            // Tambah kolom baru
            $table->decimal('sisa_pembayaran_hpp', 15, 2)->nullable()->after('down_payment');

            // Ubah nama kolom lama (pastikan kolom sisa_pembayaran sudah ada)
            if (Schema::hasColumn('purchase_orders', 'sisa_pembayaran')) {
                $table->renameColumn('sisa_pembayaran', 'sisa_pembayaran_hargajual');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            //
        });
    }
};
