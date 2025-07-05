<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('teacher_id')->constrained();
            $table->enum('evaluation_type', ['homework', 'quiz', 'exam', 'project']);
            $table->decimal('score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->date('date');
            $table->enum('semester', ['1', '2']);
            $table->string('academic_year');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('grades');
    }
};