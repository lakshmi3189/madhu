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
        Schema::create('active_demand_masters', function (Blueprint $table) {
            $table->id();
            $table->string('admission_no');            
            $table->string('roll_no');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->integer('class_id');
            $table->string('class_name');
            $table->integer('section_id');
            $table->string('section_name');
            $table->date('dob');
            $table->date('admission_date');
            $table->integer('gender_id');
            $table->string('gender_name');    
            $table->string('email')->nullable();
            $table->bigInteger('mobile');
            $table->bigInteger('aadhar_no')->nullable();
            $table->string('disability');
            $table->integer('category_id');
            $table->string('category_name');
            $table->integer('caste_id');
            $table->string('caste_name');
            $table->integer('religion_id');
            $table->string('religion_name');
            $table->integer('house_ward_id');
            $table->string('house_ward_name');
            $table->string('upload_image')->nullable();
            $table->string('fathers_name')->nullable();
            $table->string('mothers_name')->nullable();
            $table->string('fee_head_id')->nullable();
            $table->string('fee_head_name')->nullable();
            $table->string('fee_head_c_name')->nullable();
            $table->string('fee_head_amount')->nullable();
            $table->string('school_wise_scholarship_amount')->nullable();
            $table->string('student_wise_scholarship_amount')->nullable();
            $table->string('fee_concession_name')->nullable();
            $table->string('fee_concession_name')->nullable();
            $table->string('fee_concession_amount')->nullable();
            $table->string('created_by');
            $table->string('school_id');
            $table->string('academic_year');
            $table->smallInteger('version_no')->default(0); //version_no: 0->initially added, 1 and so on->no of change
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_demand_masters');
    }
};
