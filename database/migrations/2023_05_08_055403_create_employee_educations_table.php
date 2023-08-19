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
        Schema::create('employee_educations', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_tbl_id');
            $table->integer('exam_passed_id')->nullable();
            $table->string('exam_passed_name')->nullable();
            $table->string('board_uni_inst')->nullable();
            $table->string('passing_year')->nullable();
            $table->integer('div_grade_id')->nullable();
            $table->string('div_grade_name')->nullable();
            $table->integer('marks_obtained')->nullable();
            $table->integer('total_marks')->nullable();
            $table->integer('percentage')->nullable();
            $table->string('upload_edu_doc')->nullable();
            $table->integer('version_no')->default(0); 
            $table->integer('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_educations');
    }
};
