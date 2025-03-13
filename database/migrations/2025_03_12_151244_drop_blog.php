<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP TABLE IF EXISTS blogs CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string("author");
            $table->unsignedBigInteger("student_id");
            $table->unsignedBigInteger("tutor_id");
            $table->foreign("tutor_id")->on("tutors")->references("id")->onDelete("cascade");
            $table->foreign("student_id")->on("students")->references("id")->onDelete("cascade");
            $table->text("title");
            $table->string("content");
            $table->string('author_role');
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }
};
