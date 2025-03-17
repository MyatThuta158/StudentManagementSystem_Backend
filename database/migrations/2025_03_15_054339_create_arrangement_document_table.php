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
        Schema::create('arrangement_document', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("arrange_id");
            $table->foreign("arrange_id")
                ->references('id')->on('arrangings')
                ->onDelete('cascade');
            $table->string("feedback");
            $table->unsignedBigInteger("document_id");
            $table->foreign("document_id")->references("id")->on("document")->onDelete('cascade');
            
            $table->unsignedBigInteger('created_by');
            $table->enum("created_type",['tutor','student']);
            $table->enum("status",['canceled','accepted','finished','watched']);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arrangement_document');
    }
};
