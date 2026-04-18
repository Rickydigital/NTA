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
        Schema::create('progression_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->foreignId('from_program_level_id')->constrained('program_levels')->restrictOnDelete();
            $table->foreignId('to_program_level_id')->nullable()->constrained('program_levels')->nullOnDelete();

            $table->decimal('min_gpa_required', 8, 2)->nullable();
            $table->unsignedInteger('max_failed_courses_allowed')->default(0);

            $table->boolean('blocked_by_disco')->default(true);
            $table->boolean('blocked_by_fail_oral')->default(true);
            $table->boolean('requires_manual_approval')->default(false);

            $table->string('decision')->default('proceed'); // proceed, retained, disco, manual_review, completed
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['program_id', 'from_program_level_id']);
            $table->unique(['program_id', 'from_program_level_id', 'decision'], 'pr_unique_program_from_level_decision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progression_rules');
    }
};