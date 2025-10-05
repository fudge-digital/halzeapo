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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');

            $table->string('jenis_produksi')->nullable();
            $table->string('kode_desain')->nullable();
            $table->string('desain_approve')->nullable(); // path to storage
            $table->string('bahan')->nullable();
            $table->string('ukuran')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('harga_pokok_penjualan', 15, 2)->default(0);
            $table->decimal('total_hpp', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->decimal('total_harga_jual', 15, 2)->default(0);

            $table->string('po_press')->nullable();
            $table->string('po_print')->nullable();
            $table->string('po_press_print')->nullable();

            $table->string('fqc_us')->nullable();
            $table->string('fqc_la')->nullable();
            $table->string('fqc_jt')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
