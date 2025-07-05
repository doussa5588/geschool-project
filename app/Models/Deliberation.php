<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deliberation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year',
        'semester',
        'average',
        'decision',
        'mention',
        'comments',
        'deliberation_date',
        'validated_by',
    ];

    protected $casts = [
        'average' => 'decimal:2',
        'deliberation_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Accesseurs
    public function getDecisionFrenchAttribute()
    {
        switch ($this->decision) {
            case 'pass':
                return 'Admis';
            case 'fail':
                return 'Ajourné';
            case 'repeat':
                return 'Redoublant';
            default:
                return 'Non défini';
        }
    }

    public function getFormattedAverageAttribute()
    {
        return number_format($this->average, 2) . '/20';
    }

    // Scopes
    public function scopeByYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopePassed($query)
    {
        return $query->where('decision', 'pass');
    }

    public function scopeFailed($query)
    {
        return $query->where('decision', 'fail');
    }
}