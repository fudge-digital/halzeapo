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
            $table->decimal('harga_pokok_penjualan', 10, 2)->change();
            $table->decimal('total_hpp', 10, 2)->change();
            $table->decimal('harga_jual', 10, 2)->change();
            $table->decimal('total_harga_jual', 10, 2)->change();
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
