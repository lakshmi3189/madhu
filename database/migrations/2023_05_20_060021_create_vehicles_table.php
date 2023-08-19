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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_no')->unique();
            $table->string('vehicle_model');
            $table->year('year_made');
            $table->string('engine_no')->unique();
            $table->integer('max_seating_capacity');
            $table->string('registration_no')->unique();
            $table->integer('chassis_no')->unique();
            $table->string('vehicle_photo')->nullable();
            $table->string('tax_paid_date')->nullable();
            $table->string('tax_expiry_date')->nullable();
            $table->string('pollution_control_date')->nullable();
            $table->string('pollution_expiry_date')->nullable();
            $table->string('fitness_date')->nullable();
            $table->string('fitness_expiry_date')->nullable();
            $table->string('gprs');
            $table->string('ip_address');
            $table->string('school_id')->nullable();
            $table->string('academic_year')->nullable();
            $table->string('created_by')->nullable();
            $table->smallInteger('is_deleted')->default(0);
            $table->smallInteger('is_active')->default(1); 
            $table->smallInteger('version_no')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
