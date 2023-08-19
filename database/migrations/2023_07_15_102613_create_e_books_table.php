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
        Schema::create('e_books', function (Blueprint $table) {
            $table->id();
            $table->string('book_name');
            $table->string('author_name');
            $table->string('publish_by');
            $table->date('published_date');
            $table->integer('price');
            $table->text('ebook_docs');
            $table->text('cover_pic_docs');
            $table->bigInteger('school_id')->nullable();    //common for all table         
            $table->bigInteger('created_by')->nullable();   //common for all table   
            $table->string('ip_address');                   //common for all table   
            $table->integer('version_no')->default(0);      //common for all table   
            $table->smallInteger('status')->default(1);     //1-Active, 2-Not Active
            $table->text('json_logs');                   //common for all table   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_books');
    }
};
