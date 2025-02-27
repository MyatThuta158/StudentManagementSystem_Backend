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
            $table->string("title");
            $table->text("content");
            $table->string('author_role');
            $table->text('DocumentFile')->nullable();
            $table->unsignedBigInteger("student_id")->nullable();
            $table->unsignedBigInteger("tutor_id")->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign("tutor_id")->references("id")->on("tutors")->onDelete("cascade");
            $table->foreign("student_id")->references("id")->on("students")->onDelete("cascade");
        });
    }
};
