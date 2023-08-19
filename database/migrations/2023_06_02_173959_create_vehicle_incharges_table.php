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
        Schema::create('vehicle_incharges', function (Blueprint $table) {
            $table->id();
            $table->string('incharge_name');
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->string('aadhar_no')->unique();
            $table->string('address');
            $table->integer('country_id');
            $table->integer('state_id');
            $table->integer('city_id');
            $table->string('academic_year');                //common for all table
            $table->bigInteger('school_id');                //common for all table         
            $table->bigInteger('created_by');               //common for all table   
            $table->string('ip_address');                   //common for all table   
            $table->integer('version_no')->default(0);      //common for all table   
            $table->smallInteger('status')->default(1);     //common for all table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_incharges');
    }
};
