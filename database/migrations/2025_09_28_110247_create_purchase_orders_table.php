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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->default(DB::raw('CURRENT_DATE'));
            $table->string('customer');
            $table->string('no_spk')->nullable();
            $table->boolean('desain_approve')->default(false);
            $table->string('kode_desain')->nullable();
            $table->string('po_press')->nullable();
            $table->string('po_print')->nullable();
            $table->string('bahan')->nullable();
            $table->string('ukuran')->nullable();
            $table->integer('quantity');
            $table->string('fqc_us')->nullable();
            $table->string('fqc_la')->nullable();
            $table->string('fqc_jt')->nullable();
            $table->string('no_invoice')->nullable();
            $table->date('tanggal_kirim')->nullable();
            $table->enum('status', [
                'PENDING_FINANCE',
                'APPROVED_FINANCE',
                'REJECTED',
                'IN_PRODUCTION',
                'READY_TO_SHIP',
                'SHIPPED'
            ])->default('PENDING_FINANCE');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('produced_by')->nullable()->constrained('users');
            $table->foreignId('shipped_by')->nullable()->constrained('users');
            $table->decimal('down_payment', 10, 2)->nullable();
            $table->decimal('sisa_pembayaran', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
