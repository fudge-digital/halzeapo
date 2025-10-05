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
        Schema::table('enum', function (Blueprint $table) {
            DB::statement("
            ALTER TABLE purchase_orders 
            MODIFY COLUMN status ENUM(
                'PENDING_FINANCE',
                'APPROVED_FINANCE',
                'REJECTED',
                'QUEUE_PRODUCTION',
                'PENDING_PRODUCTION',
                'IN_PRODUCTION',
                'DONE_PRODUCTION',
                'READY_TO_SHIP',
                'SHIPPED'
            ) NOT NULL DEFAULT 'PENDING_FINANCE'
        ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enum', function (Blueprint $table) {
            //
        });
    }
};
