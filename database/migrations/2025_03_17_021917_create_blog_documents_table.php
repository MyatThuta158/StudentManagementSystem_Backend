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
        Schema::create('blog_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("blog_id");
            $table->unsignedBigInteger("document_id");
            $table->foreign("blog_id")->references("id")->on("blogs")->onDelete('cascade');
            $table->foreign("document_id")->references("id")->on("document")->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_documents');
    }
};
