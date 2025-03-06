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
        Schema::create('meeting_detail', function (Blueprint $table) {
            $table->id();
            $table->timestampTz("arrange_date");
            $table->enum("meeting_type",['online',"offline"]);
            $table->string("location")->nullable();
            $table->string("meeting_link")->nullable();
            $table->string("online_meeting_application_type"); // example : zoom , whatapps, teams , apple
            $table->unsignedBigInteger("arrange_id");
            $table->foreign("arrange_id")->references('id')->on('arrangings')->cascadeOnDelete();
            $table->string('topic');
            $table->enum("status",["cancelled","pending","completed", "reschelduled"]);
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_detail');
    }
};
