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
            $table->id(); // Primary key
            $table->string('fullName', 200); // Full name column
            $table->string('email', 200)->unique(); // Email column, must be unique
            $table->string('password', 255); // Password column
            $table->unsignedBigInteger('role_id'); // Role foreign key column
            $table->timestamps(); // Created at and updated at columns

            // Add a foreign key constraint for role_id (assuming roles table exists)
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
