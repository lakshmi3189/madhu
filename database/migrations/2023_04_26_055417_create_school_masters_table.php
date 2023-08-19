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
        Schema::create('school_masters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_type_id')->default(0);
            $table->string('school_name');
            $table->string('contact_person_name');
            $table->string('contact_person_mobile');
            $table->string('contact_person_email');
            $table->string('user_name');
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->string('school_code')->nullable();
            $table->string('logo')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->string('address')->nullable();
            $table->integer('pincode')->nullable();
            $table->string('fax_no')->nullable();
            $table->string('remark')->nullable();
            $table->string('academic_year');
            $table->string('ip_address');                   //common for all table   
            $table->integer('version_no')->default(0);      //common for all table 
            $table->text('json_logs');                      //common for all table  
            $table->smallInteger('status')->default(0);     //0-Not Active, 1-Active           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_masters');
    }
};
