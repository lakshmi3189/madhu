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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('user_id')->nullable();
            $table->string('user_type')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('c_password');
            $table->rememberToken();
            $table->string('school_id')->nullable();
            $table->string('ip_address');
            $table->smallInteger('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    // public function up(): void
    // {
    //     Schema::create('users', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('name');           
    //         $table->bigInteger('user_id');
    //         $table->string('email')->unique();
    //         $table->timestamp('email_verified_at')->nullable();
    //         $table->string('password');           
    //         $table->rememberToken();
    //         $table->string('school_id')->nullable();
    //         $table->string('ip_address');
    //         $table->smallInteger('status')->default(0);            
    //         $table->timestamps();
    //     });
    // }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
