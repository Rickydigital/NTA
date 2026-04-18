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
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_exam_number_id')->constrained('student_exam_numbers')->restrictOnDelete();
            $table->foreignId('student_level_placement_id')->constrained('student_level_placements')->restrictOnDelete();
            $table->foreignId('exam_session_id')->constrained()->restrictOnDelete();
            $table->foreignId('gpa_classification_id')->nullable()->constrained('gpa_classifications')->nullOnDelete();

            $table->unsignedInteger('total_courses')->default(0);
            $table->decimal('total_grade_points', 12, 2)->default(0);
            $table->decimal('gpa', 8, 2)->default(0);

            $table->string('final_comment')->nullable();
            $table->string('progression_decision')->nullable(); // proceed, retained, disco, manual_review, completed

            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('generated_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['student_id', 'exam_session_id'], 'er_unique_student_session');
            $table->index(['student_id', 'is_published']);
            $table->index(['exam_session_id', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};