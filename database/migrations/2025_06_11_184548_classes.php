<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->foreignId('level_id')->constrained('levels')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('academic_year');
            $table->integer('capacity')->default(30);
            $table->string('room')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Index pour les recherches fréquentes
            $table->index(['academic_year', 'is_active']);
            $table->index(['level_id', 'department_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('classes');
    }
};