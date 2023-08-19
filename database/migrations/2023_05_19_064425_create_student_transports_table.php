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
        Schema::create('student_transports', function (Blueprint $table) {
            $table->id();
            $table->integer('std_tbl_id');
            $table->string('roll_no');  
            $table->string('full_name'); 
            $table->string('email');
            $table->bigInteger('mobile');
            $table->integer('route_id');                      
            $table->string('route_name');
            $table->string('pick_up_point_name');
            $table->string('bus_no');
            $table->string('applicable_from');
            $table->string('created_by');
            $table->integer('school_id');
            $table->string('academic_year');
            $table->string('ip_address');
            $table->smallInteger('is_deleted')->default(0);
            $table->smallInteger('is_active')->default(1);            
            $table->smallInteger('version_no')->default(0); //version_no: 0->initially added, 1 and so on->no of change           
            $table->timestamps();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_transports');
    }
};
