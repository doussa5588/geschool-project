<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

     protected $fillable = [
        'student_id',
        'subject_id',
        'date',
        'status',
        'justification',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Accesseurs
    public function getStatusFrenchAttribute()
    {
        switch ($this->status) {
            case 'present':
                return 'Présent';
            case 'absent':
                return 'Absent';
            case 'late':
                return 'Retard';
            case 'excused':
                return 'Excusé';
            default:
                return 'Non défini';
        }
    }

    // Scopes
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}
