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
        Schema::create('arrangings', function (Blueprint $table) {
            $table->id();
            $table->dateTimeTz("arrange_date");
            $table->unsignedBigInteger("student_id")->nullable();
            $table->unsignedBigInteger("tutor_id")->nullable();
            $table->enum("type",["meeting","assignments"]);
            $table->foreign("tutor_id")->on("tutors")->references("id")->onDelete("cascade");
            $table->foreign("student_id")->on("students")->references("id")->onDelete("cascade");
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arrangings');
    }
};
