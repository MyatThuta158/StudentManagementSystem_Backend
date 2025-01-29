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
        Schema::create('enrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\Student::class,'student_id');
            $table->foreignIdFor(App\Models\Admin::class,'admin_id');
            $table-> foreignIdFor(App\Models\Course::class,'course_id');
            $table->enum('status',['pending','approved','rejected']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrolls');
    }
};
