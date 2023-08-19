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
        Schema::create('fee_definitions', function (Blueprint $table) {
            $table->id();            
            $table->integer('class_id');
            $table->integer('jan_fee');
            // $table->integer('jan_bus_fee');
            $table->integer('feb_fee');
            // $table->integer('feb_bus_fee');
            $table->integer('mar_fee');
            // $table->integer('mar_bus_fee');
            $table->integer('apr_fee');
            // $table->integer('apr_bus_fee');
            $table->integer('may_fee');
            // $table->integer('may_bus_fee');
            $table->integer('jun_fee');
            $table->integer('jun_bus_fee');
            $table->integer('jul_fee');
            // $table->integer('jul_bus_fee');
            $table->integer('aug_fee');
            // $table->integer('aug_bus_fee');
            $table->integer('sep_fee');
            // $table->integer('sep_bus_fee');
            $table->integer('oct_fee');
            // $table->integer('oct_bus_fee');
            $table->integer('nov_fee');
            // $table->integer('nov_bus_fee');
            $table->integer('dec_fee');
            // $table->integer('dec_bus_fee');            
            $table->string('academic_year')->nullable();                //common for all table
            $table->bigInteger('school_id')->nullable();                //common for all table         
            $table->bigInteger('created_by')->nullable();               //common for all table   
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
        Schema::dropIfExists('fee_definitions');
    }
};
