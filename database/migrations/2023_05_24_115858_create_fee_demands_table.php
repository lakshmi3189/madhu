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
        Schema::create('fee_demands', function (Blueprint $table) {
            $table->id();
            $table->string('fy_id');
            $table->integer('month_no');
            $table->date('demand_date');
            $table->integer('student_id');
            $table->integer('class_fee_master_id');
            $table->string('fee_head');
            $table->integer('amount');
            $table->integer('late_fee');
            $table->date('payment_date');
            $table->integer('payment_id');
            $table->string('remark');
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
        Schema::dropIfExists('fee_demands');
    }
};
