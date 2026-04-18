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
        Schema::create('student_course_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_exam_number_id')->constrained('student_exam_numbers')->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('exam_session_id')->constrained()->restrictOnDelete();
            $table->foreignId('grade_id')->constrained()->restrictOnDelete();

            $table->decimal('raw_score', 8, 2)->nullable();
            $table->decimal('grade_point_snapshot', 8, 2)->nullable();
            $table->string('comment_snapshot')->nullable();

            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->string('status')->default('draft'); // draft, approved, rejected
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['student_id', 'course_id', 'exam_session_id'], 'scr_unique_student_course_session');
            $table->index(['student_id', 'exam_session_id']);
            $table->index(['course_id', 'exam_session_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_course_results');
    }
};