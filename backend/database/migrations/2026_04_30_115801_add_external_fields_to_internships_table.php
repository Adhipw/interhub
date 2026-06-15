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
        Schema::table('internships', function (Blueprint $table) {
            $table->boolean('is_external')->default(false)->after('id');
            $table->string('external_source')->nullable()->after('is_external');
            $table->string('external_id')->nullable()->after('external_source');
            $table->string('external_url')->nullable()->after('external_id');
            $table->json('external_metadata')->nullable()->after('external_url');

            // Allow pending_review status
            // Note: status column is already a string with 'draft' default.
            // We just need to ensure the application logic handles 'pending_review'.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            //
        });
    }
};
