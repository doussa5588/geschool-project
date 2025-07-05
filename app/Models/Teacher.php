<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'specialization',
        'hire_date',
        'salary',
        'status',
        'department_id',
        'is_active',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relations

    /**
     * Get the user that owns the teacher
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that owns the teacher
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the subjects for the teacher
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    /**
     * Get the schedules for the teacher
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    /**
     * Get the grades given by the teacher
     */
    public function grades()
    {
        return $this->hasMany(Grade::class, 'teacher_id');
    }

    /**
     * Get the attendances recorded by the teacher
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'recorded_by');
    }

    /**
     * Get all classes this teacher teaches (through subjects)
     */
    public function classes()
    {
        return $this->hasManyThrough(
            Classe::class,
            Subject::class,
            'teacher_id', // Foreign key on subjects table
            'id', // Foreign key on classes table
            'id', // Local key on teachers table
            'id' // Local key on subjects table
        )->distinct();
    }

    // Accesseurs

    /**
     * Get teacher's experience in years
     */
    public function getExperienceYearsAttribute()
    {
        if (!$this->hire_date) {
            return 0;
        }
        return Carbon::parse($this->hire_date)->diffInYears(now());
    }

    /**
     * Get teacher's seniority level
     */
    public function getSeniorityLevelAttribute()
    {
        $years = $this->experience_years;
        
        if ($years >= 10) {
            return 'senior';
        } elseif ($years >= 5) {
            return 'intermediate';
        } else {
            return 'junior';
        }
    }

    /**
     * Get seniority level in French
     */
    public function getSeniorityLevelFrenchAttribute()
    {
        $level = $this->seniority_level;
        
        switch ($level) {
            case 'senior':
                return 'Expérimenté';
            case 'intermediate':
                return 'Intermédiaire';
            case 'junior':
                return 'Débutant';
            default:
                return 'Non défini';
        }
    }

    /**
     * Check if teacher is active
     */
    public function getIsActiveStatusAttribute()
    {
        return $this->is_active && $this->status === 'active';
    }

    /**
     * Get formatted hire date
     */
    public function getFormattedHireDateAttribute()
    {
        if (!$this->hire_date) {
            return 'Non définie';
        }
        return Carbon::parse($this->hire_date)->format('d/m/Y');
    }

    /**
     * Get formatted salary
     */
    public function getFormattedSalaryAttribute()
    {
        if (!$this->salary) {
            return 'Non défini';
        }
        return number_format($this->salary, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Get status in French
     */
    public function getStatusFrenchAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'Actif';
            case 'inactive':
                return 'Inactif';
            case 'suspended':
                return 'Suspendu';
            default:
                return 'Non défini';
        }
    }

    /**
     * Get total credits taught
     */
    public function getTotalCreditsAttribute()
    {
        return $this->subjects()->sum('credits');
    }

    /**
     * Get subjects count
     */
    public function getSubjectsCountAttribute()
    {
        return $this->subjects()->count();
    }

    /**
     * Get active subjects count
     */
    public function getActiveSubjectsCountAttribute()
    {
        return $this->subjects()->where('is_active', true)->count();
    }

    /**
     * Get workload description
     */
    public function getWorkloadDescriptionAttribute()
    {
        $subjectsCount = $this->subjects_count;
        $totalCredits = $this->total_credits;
        
        if ($subjectsCount === 0) {
            return 'Aucune matière assignée';
        }
        
        return "{$subjectsCount} matière(s) - {$totalCredits} crédits";
    }

    /**
     * Get classes count through subjects
     */
    public function getClassesCountAttribute()
    {
        return $this->subjects()
            ->with('classes')
            ->get()
            ->pluck('classes')
            ->flatten()
            ->unique('id')
            ->count();
    }

    /**
     * Get total students count (through classes via subjects)
     */
    public function getTotalStudentsCountAttribute()
    {
        $classIds = $this->subjects()
            ->with('classes')
            ->get()
            ->pluck('classes')
            ->flatten()
            ->pluck('id')
            ->unique();

        return Student::whereIn('class_id', $classIds)->count();
    }

    /**
     * Get departments count (for teachers teaching in multiple departments)
     */
    public function getDepartmentsCountAttribute()
    {
        return $this->subjects()
            ->join('departments', 'subjects.department_id', '=', 'departments.id')
            ->distinct('departments.id')
            ->count();
    }

    /**
     * Get full name through user relationship
     */
    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->name : 'Utilisateur supprimé';
    }

    /**
     * Get average grade given by teacher
     */
    public function getAverageGradeGivenAttribute()
    {
        return $this->grades()->avg('score') ?: 0;
    }

    /**
     * Get total grades given
     */
    public function getTotalGradesGivenAttribute()
    {
        return $this->grades()->count();
    }

    /**
     * Get this month's grades count
     */
    public function getThisMonthGradesCountAttribute()
    {
        return $this->grades()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
    }

    // Scopes

    /**
     * Scope for active teachers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope for teachers by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope for teachers by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('user', function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        })->orWhere('employee_number', 'like', "%{$search}%")
          ->orWhere('specialization', 'like', "%{$search}%");
    }

    /**
     * Scope for teachers with subjects
     */
    public function scopeWithSubjects($query)
    {
        return $query->has('subjects');
    }

    /**
     * Scope for teachers without subjects
     */
    public function scopeWithoutSubjects($query)
    {
        return $query->doesntHave('subjects');
    }

    /**
     * Scope for senior teachers (10+ years experience)
     */
    public function scopeSenior($query)
    {
        return $query->whereRaw('DATEDIFF(NOW(), hire_date) >= ?', [365 * 10]);
    }

    /**
     * Scope for teachers by experience level
     */
    public function scopeByExperience($query, $level)
    {
        switch ($level) {
            case 'senior':
                return $query->whereRaw('DATEDIFF(NOW(), hire_date) >= ?', [365 * 10]);
            case 'intermediate':
                return $query->whereRaw('DATEDIFF(NOW(), hire_date) >= ? AND DATEDIFF(NOW(), hire_date) < ?', [365 * 5, 365 * 10]);
            case 'junior':
                return $query->whereRaw('DATEDIFF(NOW(), hire_date) < ?', [365 * 5]);
            default:
                return $query;
        }
    }

    // Méthodes utilitaires

    /**
     * Check if teacher can teach a specific subject
     */
    public function canTeach(Subject $subject)
    {
        return $this->subjects->contains($subject);
    }

    /**
     * Get teacher's next schedule
     */
    public function getNextSchedule()
    {
        return $this->schedules()
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->first();
    }

    /**
     * Get teacher's weekly schedule
     */
    public function getWeeklySchedule($startDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();
        
        return $this->schedules()
            ->whereBetween('start_time', [$startDate, $endDate])
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Calculate teaching load in hours per week
     */
    public function getWeeklyTeachingHours()
    {
        // Estimation basée sur les crédits
        return $this->total_credits * 1.5; // Estimation: 1.5h par crédit
    }

    /**
     * Get teacher's performance statistics
     */
    public function getPerformanceStats()
    {
        $grades = $this->grades;
        
        return [
            'total_grades_given' => $grades->count(),
            'average_score' => $grades->avg('score'),
            'pass_rate' => $grades->where('score', '>=', $grades->avg('max_score') * 0.5)->count() / max($grades->count(), 1) * 100,
            'subjects_taught' => $this->subjects_count,
            'classes_count' => $this->classes_count,
            'students_count' => $this->total_students_count,
        ];
    }

    /**
     * Check if teacher has grades recorded
     */
    public function hasGrades()
    {
        return $this->grades()->exists();
    }

    /**
     * Check if teacher has schedules
     */
    public function hasSchedules()
    {
        return $this->schedules()->exists();
    }

    /**
     * Check if teacher can be deleted
     */
    public function canBeDeleted()
    {
        return !$this->hasGrades() && !$this->hasSchedules();
    }

    /**
     * Get all distinct classes this teacher teaches
     */
    public function getDistinctClasses()
    {
        return $this->subjects()
            ->with('classes')
            ->get()
            ->pluck('classes')
            ->flatten()
            ->unique('id');
    }

    /**
     * Get subjects by department
     */
    public function getSubjectsByDepartment()
    {
        return $this->subjects()
            ->with('department')
            ->get()
            ->groupBy('department.name');
    }
}