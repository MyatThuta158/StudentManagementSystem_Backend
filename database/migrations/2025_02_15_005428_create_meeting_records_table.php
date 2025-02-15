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
        Schema::create('meeting_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("arrange_id");
            $table->foreign("arrange_id")->on("arrangings")->references("id")->onDelete("cascade");
            $table->string("meeting_notes");
            $table->dateTimeTz("meeting_date");
            $table->string("meeting_type");
            $table->string("uploaded_document");
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_records');
    }
};
