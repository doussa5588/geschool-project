<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deliberations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->string('academic_year');
            $table->enum('semester', ['1', '2']);
            $table->decimal('average', 5, 2);
            $table->enum('decision', ['pass', 'fail', 'repeat']);
            $table->string('mention')->nullable();
            $table->text('comments')->nullable();
            $table->date('deliberation_date');
            $table->foreignId('validated_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deliberations');
    }
};