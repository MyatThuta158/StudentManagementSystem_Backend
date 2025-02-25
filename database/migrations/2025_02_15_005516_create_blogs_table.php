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
            $table->unsignedBigInteger("student_id");
            $table->unsignedBigInteger("tutor_id");
            $table->foreign("tutor_id")->on("tutors")->references("id")->onDelete("cascade");
            $table->foreign("student_id")->on("students")->references("id")->onDelete("cascade");
            $table->text("body");
            $table->string("header");
            $table->softDeletesTz();
            $table->timestampsTz();
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
