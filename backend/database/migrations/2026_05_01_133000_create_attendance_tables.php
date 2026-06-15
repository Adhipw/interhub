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
        // Add geofence fields to companies
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('geofence_radius')->default(100); // meters
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->json('check_in_location')->nullable(); // {lat, lng}
            $table->json('check_out_location')->nullable();
            $table->string('status')->default('present'); // present, late, absent
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users');
            $table->timestamp('new_check_in_at')->nullable();
            $table->timestamp('new_check_out_at')->nullable();
            $table->text('reason');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->text('reviewer_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'geofence_radius']);
        });
        Schema::dropIfExists('attendance_corrections');
        Schema::dropIfExists('attendances');
    }
};
