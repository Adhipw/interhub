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
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('industry_id')->nullable()->constrained('industries')->nullOnDelete();
        });

        Schema::table('internships', function (Blueprint $table) {
            $table->foreignId('industry_id')->nullable()->constrained('industries')->nullOnDelete();
            $table->string('category')->nullable(); // Legacy support or sub-category
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->dropForeign(['industry_id']);
            $table->dropColumn(['industry_id', 'category']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['industry_id']);
            $table->dropColumn('industry_id');
        });

        Schema::dropIfExists('industries');
    }
};
