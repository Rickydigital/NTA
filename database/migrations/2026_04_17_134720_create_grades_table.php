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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('grade_code', 50)->unique(); // A, B, C, D, F, C***
            $table->decimal('grade_point', 8, 2)->default(0);
            $table->decimal('min_score', 8, 2)->nullable();
            $table->decimal('max_score', 8, 2)->nullable();
            $table->string('comment_label')->nullable(); // PASS, FAIL, FAILS ORAL
            $table->string('result_status')->nullable(); // PASS, FAIL, SUPP, DISCO_TRIGGER
            $table->boolean('affects_gpa')->default(true);
            $table->boolean('is_pass_grade')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};