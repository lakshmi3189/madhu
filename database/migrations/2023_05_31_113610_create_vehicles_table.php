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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_no');
            $table->string('registration_no');
            $table->string('chasis_no');
            $table->bigInteger('vehicle_types_id');
            $table->string('academic_year');                //common for all table
            $table->bigInteger('school_id');                //common for all table
            $table->bigInteger('created_by');               //common for all table
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
        Schema::dropIfExists('vehicles');
    }
};