<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_items', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->enum('type', ['general', 'burial'])->default('general');
        });
    }

    public function down(): void
    {
        Schema::table('event_items', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['organization_id', 'type']);
        });
    }
};
