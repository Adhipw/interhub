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
        // Add mentor_id to applications to assign a mentor to an intern
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('mentor_user_id')->nullable()->constrained('users')->onDelete('set null');
        });

        // Mentor Tasks Table
        Schema::create('mentor_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('mentor_user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('todo'); // todo, in_progress, completed, overdue
            $table->integer('priority')->default(1); // 1: low, 2: medium, 3: high
            $table->timestamps();
            $table->softDeletes();
        });

        // Mentor Evaluations Table
        Schema::create('mentor_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('mentor_user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('summary')->nullable();
            $table->json('metrics')->nullable(); // { technical: 5, soft_skills: 4, ... }
            $table->text('recommendation')->nullable();
            $table->string('final_status')->nullable(); // recommend, not_recommend
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentor_evaluations');
        Schema::dropIfExists('mentor_tasks');
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['mentor_user_id']);
            $table->dropColumn('mentor_user_id');
        });
    }
};
