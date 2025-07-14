<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration Étudiants - Architecture par SADOU MBALLO
     */
    
    public function up()
    {
        Schema::create('etudiants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('numero_etudiant')->unique();
            $table->foreignId('classe_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date_inscription');
            $table->enum('statut_inscription', ['inscrit', 'diplome', 'abandonne'])->default('inscrit');
            $table->string('niveau_precedent')->nullable();
            $table->text('observations')->nullable();
            $table->decimal('frais_scolarite', 10, 2)->default(0);
            $table->enum('statut_paiement', ['paye', 'partiel', 'impaye'])->default('impaye');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('etudiants');
    }
};