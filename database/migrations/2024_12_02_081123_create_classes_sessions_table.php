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
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('class_id'); // Foreign key referencing 'classes' table
            $table->time('start_time'); // Start time of the session
            $table->time('end_time'); // End time of the session
            $table->boolean('attendance_marked')->default(false); // Attendance marked or not
            $table->timestamps(); // Created at and updated at columns

            // Foreign key constraint
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
