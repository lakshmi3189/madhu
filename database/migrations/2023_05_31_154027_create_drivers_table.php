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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('driver_name');
            $table->string('mobile');
            $table->string('email');
            $table->string('address');
            $table->string('license_no');
            $table->string('aadhar_no');
            $table->string('pan_no')->nullable();
            $table->string('photo_doc')->nullable();
            $table->string('aadhar_doc')->nullable();
            $table->string('license_doc')->nullable();
            $table->string('pan_doc')->nullable();
            $table->bigInteger('school_id');                //common for all table         
            $table->bigInteger('created_by');               //common for all table   
            $table->string('ip_address');                   //common for all table   
            $table->integer('version_no')->default(0);      //common for all table   
            $table->smallInteger('status')->default(1);     //1-Active, 2-Not Active
            $table->text('json_logs');                   //common for all table   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
