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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();                   // FK to users (cascade)
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();                   // FK to classes (cascade)
            $table->enum('status', ['present','absent','late']);
            // Attendance status (enum)&#8203;:contentReference[oaicite:21]{index=21}
            $table->dateTime('marked_at');              // When attendance was marked
            $table->timestamps();

            // Indexes for fast lookup: one record per student-class
            $table->unique(['student_id', 'class_id']); // Composite unique index&#8203;:contentReference[oaicite:22]{index=22}
            $table->index('student_id');
            $table->index('class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
