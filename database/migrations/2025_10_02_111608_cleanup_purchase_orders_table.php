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
            // Pastikan kolom ada sebelum drop (untuk aman)
            $cols = ['quantity','kode_desain','desain_approve','bahan','ukuran','harga_pokok_penjualan','total_hpp','harga_jual','total_harga_jual','po_press','po_print','po_press_print','fqc_us','fqc_la','fqc_jt'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('purchase_orders', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // fallback: tambahkan kembali kolom dengan default aman
            $table->integer('quantity')->default(0)->after('some_column'); // sesuaikan posisi jika perlu
            // ... tambahkan kolom lain jika perlu
        });
    }
};
