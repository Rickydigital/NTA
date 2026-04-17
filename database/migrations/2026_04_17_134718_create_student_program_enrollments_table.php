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
        Schema::create('student_program_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->foreignId('intake_academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->date('enrollment_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->string('status')->default('active'); // active, completed, deferred, discontinued
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'program_id']);
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_program_enrollments');
    }
};


