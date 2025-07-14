<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration Users - Développée par SADOU MBALLO
     * Architecture de base du système GeSchool
     */
    
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'professeur', 'etudiant', 'parent'])->default('etudiant');
            $table->enum('status', ['actif', 'inactif', 'suspendu'])->default('actif');
            $table->string('telephone')->nullable();
            $table->text('adresse')->nullable();
            $table->date('date_naissance')->nullable();
            $table->enum('genre', ['masculin', 'feminin'])->nullable();
            $table->string('photo')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};