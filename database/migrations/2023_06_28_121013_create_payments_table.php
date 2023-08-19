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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id'); 
            $table->bigInteger('fee_collection_id');
            $table->bigInteger('payment_mode_id');
            $table->integer('is_paid');
            $table->date('payment_date');
            $table->string('cheque_no')->nullable(); 
            $table->string('dd_no')->nullable();
            $table->integer('bank_approved')->default(0);
            $table->bigInteger('school_id');                //common for all table         
            $table->string('academic_year');                //common for all table
            $table->bigInteger('created_by');               //common for all table   
            $table->string('ip_address');                   //common for all table   
            $table->integer('version_no')->default(0);      //common for all table   
            $table->text('json_logs');                      //common for all table 
            $table->smallInteger('status')->default(1);     //1-Active, 2-Not Active  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
