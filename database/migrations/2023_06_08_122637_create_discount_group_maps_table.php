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
        Schema::create('discount_group_maps', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->bigInteger('discount_group_id');
            $table->bigInteger('discount_percent');
            $table->bigInteger('class_fee_id');
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
        Schema::dropIfExists('discount_group_maps');
    }
};
