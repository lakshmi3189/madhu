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
        Schema::create('student_siblings', function (Blueprint $table) {
            $table->id();
            $table->integer('std_tbl_id');
            $table->string('sibling_name');                      
            $table->string('sibling_class');
            $table->string('sibling_section');
            $table->string('sibling_admission_no')->nullable();
            $table->string('sibling_roll_no')->nullable();
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
        Schema::dropIfExists('student_siblings');
    }
};
