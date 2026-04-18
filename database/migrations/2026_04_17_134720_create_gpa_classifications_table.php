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
        Schema::create('gpa_classifications', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Pass, Fail, Disco
            $table->decimal('min_gpa', 8, 2);
            $table->decimal('max_gpa', 8, 2);
            $table->string('classification_code')->unique(); // PASS, FAIL, DISCO
            $table->string('final_comment');
            $table->string('progression_action')->nullable(); // proceed, retained, disco, manual_review, completed
            $table->unsignedInteger('priority_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gpa_classifications');
    }
};