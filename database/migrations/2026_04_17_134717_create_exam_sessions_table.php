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
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->string('name'); // Semester I, Semester II, Annual Final
            $table->string('session_type')->nullable(); // semester, annual, supplementary
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_result_entry_open')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['academic_year_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};