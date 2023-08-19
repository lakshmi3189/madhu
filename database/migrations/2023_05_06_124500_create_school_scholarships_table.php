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
        Schema::create('school_scholarships', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id');
            $table->integer('class_id');
            $table->integer('fee_head_id');
            $table->integer('discount_amount');
            $table->string('academic_year');
            $table->integer('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_scholarships');
    }
};
