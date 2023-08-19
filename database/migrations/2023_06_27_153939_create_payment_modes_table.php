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
        Schema::create('payment_modes', function (Blueprint $table) {
            $table->id();
            $table->string('payment_mode_name'); 
            $table->string('ip_address');                   //common for all table   
            $table->integer('version_no')->default(0);                  //common for all table   
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
        Schema::dropIfExists('payment_modes');
    }
};
