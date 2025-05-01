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
        Schema::create('batch_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')
                ->constrained('batches')
                ->cascadeOnDelete();                   // FK to batches (cascade on delete)&#8203;:contentReference[oaicite:9]{index=9}
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();                   // FK to users (cascade on delete)&#8203;:contentReference[oaicite:10]{index=10}
            $table->timestamps();

            // Prevent duplicate enrollment: one student per batch
            $table->unique(['batch_id', 'student_id']);     // Composite unique index&#8203;:contentReference[oaicite:11]{index=11}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_student');
    }
};
