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
        Schema::create('class_fee_masters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('class_id');
            $table->bigInteger('fee_head_id');
            $table->decimal('fee_amount', 18, 2);
            $table->decimal('discount', 18, 2);
            $table->decimal('net_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);
            $table->decimal('jan_fee', 18, 2);

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
        Schema::dropIfExists('class_fee_masters');
    }
};
