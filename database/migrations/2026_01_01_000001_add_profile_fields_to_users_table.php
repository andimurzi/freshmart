<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('customer')->after('password'); // admin | customer
            $table->string('phone', 25)->nullable()->after('role');
            $table->text('address')->nullable()->after('phone');
            $table->string('gender', 1)->nullable()->after('address'); // L | P
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'address', 'gender']);
        });
    }
};
