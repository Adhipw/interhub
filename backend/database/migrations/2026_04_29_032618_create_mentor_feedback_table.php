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
        Schema::create('mentor_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('mentor_user_id')->constrained('users')->onDelete('cascade');
            $table->text('content')->nullable();
            $table->json('assessment')->nullable(); // Stores {technical: 5, soft_skills: 4, etc}
            $table->string('status')->default('draft'); // draft, submitted
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentor_feedback');
    }
};
