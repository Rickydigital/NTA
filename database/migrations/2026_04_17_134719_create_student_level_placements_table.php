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
        Schema::create('student_level_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_program_enrollment_id')->constrained()->restrictOnDelete();
            $table->foreignId('program_level_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(true);
            $table->string('progression_status')->nullable();
            $table->string('placement_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['student_program_enrollment_id', 'is_current'],
                'slp_enrollment_current_idx'
            );

            $table->index(
                ['program_level_id', 'academic_year_id'],
                'slp_level_year_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_level_placements');
    }
};