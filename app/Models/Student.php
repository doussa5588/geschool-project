<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_number',
        'class_id',
        'academic_year',
        'enrollment_date',
        'status',
        'parent_contact',
        'emergency_contact',
        'is_active',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function deliberations()
    {
        return $this->hasMany(Deliberation::class);
    }

    // Accesseurs
    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->name : 'Utilisateur supprimé';
    }

    public function getAverageGradeAttribute()
    {
        return $this->grades()->avg('score') ?: 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }
}