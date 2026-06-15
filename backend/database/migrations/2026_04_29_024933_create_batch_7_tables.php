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
        Schema::create('company_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('hr'); // owner, hr, mentor
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'user_id']);
        });

        Schema::create('interview_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('interviewer_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('scheduled_at');
            $table->string('type')->default('online'); // online, offline
            $table->string('meeting_link')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('hr_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['interviewer_id']);
            $table->dropColumn(['interviewer_id', 'hr_notes']);
        });
        Schema::dropIfExists('interview_schedules');
        Schema::dropIfExists('company_members');
    }
};
