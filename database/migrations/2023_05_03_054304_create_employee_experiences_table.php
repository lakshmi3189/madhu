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
        Schema::create('employee_experiences', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_tbl_id');
            $table->string('name_of_org')->nullable();
            $table->string('position_head')->nullable();
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->integer('salary')->nullable();
            $table->string('pay_grade')->nullable();
            $table->string('upload_exp_letter')->nullable();
            $table->integer('version_no')->default(0); 
            $table->integer('is_deleted')->default(0);  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_experiences');
    }
};
