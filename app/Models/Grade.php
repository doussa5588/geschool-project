<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'teacher_id',
        'evaluation_type',
        'score',
        'max_score',
        'date',
        'semester',
        'academic_year',
        'comments',
    ];

    protected $casts = [
        'date' => 'date',
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function getPercentageAttribute()
    {
        return ($this->score / $this->max_score) * 100;
    }
}