<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('name');
            $table->string('phone', 25);
            $table->string('email');
            $table->text('address');
            $table->string('city', 50);
            $table->date('delivery_date');
            $table->string('delivery_time', 10);   // pagi | siang | sore
            $table->string('payment_method', 20);  // cod | transfer | ewallet
            $table->text('notes')->nullable();
            $table->boolean('is_gift')->default(false);
            $table->unsignedInteger('total');
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
