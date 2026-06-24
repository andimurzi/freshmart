<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Extend payment_method to include 'manual' and 'qris'
            $table->string('payment_method', 20)->change(); // cod | transfer | ewallet | manual | qris

            // Bukti pembayaran (untuk transfer manual & QRIS)
            $table->string('payment_proof')->nullable()->after('payment_method');

            // Status pembayaran terpisah dari status pesanan
            $table->string('payment_status', 20)->default('unpaid')->after('payment_proof');
            // unpaid | waiting_verification | paid | failed

            // Catatan verifikasi dari admin
            $table->text('payment_note')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_proof', 'payment_status', 'payment_note']);
        });
    }
};
