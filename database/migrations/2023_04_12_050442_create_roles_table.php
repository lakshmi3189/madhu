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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
            $table->bigInteger('school_id');                //common for all table         
            $table->bigInteger('created_by');               //common for all table 
            $table->string('ip_address');                   //common for all table   
            $table->integer('version_no')->default(0);      //common for all table 
            $table->text('json_logs');                      //common for all table  
            $table->smallInteger('status')->default(1);     //0-Not Active, 1-Active       
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
