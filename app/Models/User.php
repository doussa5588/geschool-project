<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Modèle User - Architecture par SADOU MBALLO
     * Responsable du projet GeSchool
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'telephone',
        'adresse',
        'date_naissance',
        'genre',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_naissance' => 'date',
    ];

    // Relations
    public function etudiant()
    {
        return $this->hasOne(Etudiant::class);
    }

    public function professeur()
    {
        return $this->hasOne(Professeur::class);
    }

    // Accesseurs
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getAgeAttribute()
    {
        return $this->date_naissance ? $this->date_naissance->age : null;
    }

    // Méthodes utilitaires
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isProfesseur()
    {
        return $this->role === 'professeur';
    }

    public function isEtudiant()
    {
        return $this->role === 'etudiant';
    }

    public function isParent()
    {
        return $this->role === 'parent';
    }

    public function isActif()
    {
        return $this->status === 'actif';
    }
}