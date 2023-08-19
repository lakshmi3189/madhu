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
        Schema::create('employee_families', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_tbl_id');
            $table->string('f_member_name')->nullable();
            $table->integer('f_member_relation_id')->nullable();
            $table->string('f_member_relation_name')->nullable();
            $table->date('f_member_dob')->nullable();
            $table->string('upload_f_member_image')->nullable();
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
        Schema::dropIfExists('employee_families');
    }
};
