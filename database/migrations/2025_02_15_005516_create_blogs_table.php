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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string("author");
            $table->string("title"); // ✅ Renamed from "header"
            $table->text("content");
            $table->unsignedBigInteger("student_id")->nullable(); // ✅ Now nullable
            $table->unsignedBigInteger("tutor_id")->nullable(); // ✅ Now nullable
            $table->timestampsTz();
            $table->softDeletesTz();

            // ✅ Move foreign keys here to avoid potential issues
            $table->foreign("tutor_id")->references("id")->on("tutors")->onDelete("cascade");
            $table->foreign("student_id")->references("id")->on("students")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
