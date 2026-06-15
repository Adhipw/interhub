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
            $table->boolean('is_paid')->default(false)->after('location');
            $table->string('stipend')->nullable()->after('is_paid');
            $table->json('tags')->nullable()->after('status');
            // If salary_range exists and we want to rename it, but it's easier to just add stipend.
            if (Schema::hasColumn('internships', 'salary_range')) {
                $table->dropColumn('salary_range');
            }
        });
    }

    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'stipend', 'tags']);
            $table->string('salary_range')->nullable();
        });
    }
};
