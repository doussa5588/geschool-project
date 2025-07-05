<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec les enseignants
     */
    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    /**
     * Relation avec les matières
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Enseignants actifs du département
     */
    public function activeTeachers()
    {
        return $this->hasMany(Teacher::class)->where('is_active', true)->where('status', 'active');
    }

    /**
     * Matières actives du département
     */
    public function activeSubjects()
    {
        return $this->hasMany(Subject::class)->where('is_active', true);
    }

    /**
     * Scope pour les départements actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour rechercher des départements
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Accesseur pour le nombre d'enseignants
     */
    public function getTeachersCountAttribute()
    {
        return $this->teachers()->count();
    }

    /**
     * Accesseur pour le nombre d'enseignants actifs
     */
    public function getActiveTeachersCountAttribute()
    {
        return $this->activeTeachers()->count();
    }

    /**
     * Accesseur pour le nombre de matières
     */
    public function getSubjectsCountAttribute()
    {
        return $this->subjects()->count();
    }

    /**
     * Accesseur pour le nombre de matières actives
     */
    public function getActiveSubjectsCountAttribute()
    {
        return $this->activeSubjects()->count();
    }

    /**
     * Accesseur pour le total des crédits
     */
    public function getTotalCreditsAttribute()
    {
        return $this->subjects()->sum('credits');
    }

    /**
     * Accesseur pour le statut en français
     */
    public function getStatusFrenchAttribute()
    {
        return $this->is_active ? 'Actif' : 'Inactif';
    }

    /**
     * Vérifier si le département peut être supprimé
     */
    public function canBeDeleted()
    {
        return !$this->teachers()->exists() && !$this->subjects()->exists();
    }

    /**
     * Obtenir les statistiques du département
     */
    public function getStats()
    {
        return [
            'teachers_count' => $this->teachers()->count(),
            'active_teachers_count' => $this->activeTeachers()->count(),
            'subjects_count' => $this->subjects()->count(),
            'active_subjects_count' => $this->activeSubjects()->count(),
            'total_credits' => $this->subjects()->sum('credits'),
            'subjects_with_teacher' => $this->subjects()->whereNotNull('teacher_id')->count(),
            'subjects_without_teacher' => $this->subjects()->whereNull('teacher_id')->count(),
        ];
    }
}