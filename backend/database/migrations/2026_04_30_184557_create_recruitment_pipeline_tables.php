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
        Schema::create('recruitment_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('order')->default(0);
            $table->string('type')->default('screening'); // screening, interview, technical, offer, rejected, hired
            $table->integer('sla_days')->nullable(); // For reminders
            $table->timestamps();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('current_stage_id')->nullable()->after('status')->constrained('recruitment_stages')->onDelete('set null');
        });

        Schema::create('application_stage_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_stage_id')->nullable()->constrained('recruitment_stages');
            $table->foreignId('to_stage_id')->constrained('recruitment_stages');
            $table->foreignId('changed_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->timestamps();
        });

        Schema::create('screening_rubrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->onDelete('cascade');
            $table->json('criteria'); // Array of {name, weight, description}
            $table->timestamps();
        });

        Schema::create('application_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2);
            $table->json('factors'); // JSON of score breakdown and justification
            $table->boolean('is_ai_suggested')->default(false);
            $table->boolean('human_reviewed')->default(false);
            $table->foreignId('reviewer_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_scores');
        Schema::dropIfExists('screening_rubrics');
        Schema::dropIfExists('application_stage_history');
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['current_stage_id']);
            $table->dropColumn('current_stage_id');
        });
        Schema::dropIfExists('recruitment_stages');
    }
};
