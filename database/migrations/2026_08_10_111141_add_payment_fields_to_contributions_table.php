<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->unique();
            $table->enum('source', ['admin', 'self'])->default('admin');
            $table->enum('payment_status', ['completed', 'pending', 'failed'])->default('completed');
        });
    }

    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'source', 'payment_status']);
        });
    }
};
