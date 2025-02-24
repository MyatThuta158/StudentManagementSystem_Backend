<?php

use App\Models\Section;
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
        Schema::table('arrangings', function (Blueprint $table) {
            $table->dropForeignIdFor(Section::class, 'section_id');
            $table->unsignedBigInteger('created_by');
            $table->enum('creator_type',['student','tutor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arranging', function (Blueprint $table) {
            //
        });
    }
};
