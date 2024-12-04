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
        Schema::create('classes_students', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('class_id'); // Foreign key referencing 'classes' table
            $table->unsignedBigInteger('student_id'); // Foreign key referencing 'users' table
            $table->timestamps(); // Created at and updated at columns

            // Foreign key constraints
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes_students');
    }
};
