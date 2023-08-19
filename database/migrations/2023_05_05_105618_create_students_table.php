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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('admission_no');
            $table->string('roll_no');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->integer('class_id');
            $table->string('class_name');
            $table->integer('section_id')->nullable();
            $table->string('section_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('admission_date');
            $table->integer('gender_id')->nullable();
            $table->string('gender_name');

            $table->integer('blood_group_id')->nullable();
            $table->string('blood_group_name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->bigInteger('aadhar_no')->nullable();
            $table->string('disability')->nullable();

            $table->integer('category_id')->nullable();
            $table->string('category_name')->nullable();
            $table->integer('caste_id')->nullable();
            $table->string('caste_name')->nullable();
            $table->integer('religion_id')->nullable();
            $table->string('religion_name')->nullable();
            $table->integer('house_ward_id')->nullable();
            $table->string('house_ward_name')->nullable();

            $table->string('upload_image')->nullable();
            $table->string('last_school_name')->nullable();
            $table->string('last_school_address')->nullable();
            $table->string('admission_mid_session')->nullable();
            $table->string('admission_month')->nullable();
            $table->string('fathers_name')->nullable();
            $table->bigInteger('fathers_mob_no')->nullable();
            $table->integer('fathers_qualification_id')->nullable();
            $table->string('fathers_qualification_name')->nullable();
            $table->integer('fathers_occupation_id')->nullable();
            $table->string('fathers_occupation_name')->nullable();
            $table->string('fathers_email')->nullable();
            $table->bigInteger('fathers_aadhar')->nullable();
            $table->string('fathers_image')->nullable();
            $table->decimal('fathers_annual_income', 10, 2)->nullable();

            $table->string('mothers_name')->nullable();
            $table->bigInteger('mothers_mob_no')->nullable();
            $table->integer('mothers_qualification_id')->nullable();
            $table->string('mothers_qualification_name')->nullable();
            $table->integer('mothers_occupation_id')->nullable();
            $table->string('mothers_occupation_name')->nullable();
            $table->string('mothers_email')->nullable();
            $table->bigInteger('mothers_aadhar')->nullable();
            $table->string('mothers_image')->nullable();
            $table->decimal('mothers_annual_income', 10, 2)->nullable();

            $table->string('guardian_name')->nullable();
            $table->bigInteger('guardian_mob_no')->nullable();
            $table->integer('guardian_qualification_id')->nullable();
            $table->string('guardian_qualification_name')->nullable();
            $table->integer('guardian_occupation_id')->nullable();
            $table->string('guardian_occupation_name')->nullable();
            $table->string('guardian_email')->nullable();
            $table->bigInteger('guardian_aadhar')->nullable();
            $table->string('guardian_image')->nullable();
            $table->decimal('guardian_annual_income', 10, 2)->nullable();
            $table->integer('guardian_relation_id')->nullable();
            $table->string('guardian_relation_name')->nullable();

            $table->string('p_address1')->nullable();
            $table->string('p_address2')->nullable();
            $table->string('p_locality')->nullable();
            $table->string('p_landmark')->nullable();
            $table->integer('p_country_id')->nullable();
            $table->string('p_country_name')->nullable();
            $table->integer('p_state_id')->nullable();
            $table->string('p_state_name')->nullable();
            $table->integer('p_district_id')->nullable();
            $table->string('p_district_name')->nullable();
            $table->integer('p_pincode')->nullable();
            $table->string('c_address1')->nullable();
            $table->string('c_address2')->nullable();
            $table->string('c_locality')->nullable();
            $table->string('c_landmark')->nullable();
            $table->integer('c_country_id')->nullable();
            $table->string('c_country_name')->nullable();
            $table->integer('c_state_id')->nullable();
            $table->string('c_state_name')->nullable();
            $table->integer('c_district_id')->nullable();
            $table->string('c_district_name')->nullable();
            $table->integer('c_pincode')->nullable();
            
            $table->string('hobbies')->nullable();
            $table->integer('bank_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('is_transport')->nullable();
            $table->string('academic_year')->nullable();    //common for all table
            $table->bigInteger('school_id')->nullable();    //common for all table         
            $table->bigInteger('created_by')->nullable();   //common for all table   
            $table->string('ip_address');                   //common for all table   
            $table->integer('version_no')->default(0);      //common for all table   
            $table->smallInteger('status')->default(1);     //1-Active, 2-Not Active
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
