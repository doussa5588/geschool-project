<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'credits',
        'teacher_id',
        'department_id',
        'is_active',
    ];

    protected $casts = [
        'credits' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relations

    /**
     * Get the department that owns the subject
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the teacher that teaches this subject
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the grades for this subject
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get the schedules for this subject
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Get the attendances for this subject
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the classes that have this subject (through class_subjects pivot)
     */
    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'class_subjects', 'subject_id', 'class_id');
    }

    // Accesseurs

    /**
     * Get teacher name
     */
    public function getTeacherNameAttribute()
    {
        return $this->teacher ? $this->teacher->user->name : 'Non assigné';
    }

    /**
     * Get department name
     */
    public function getDepartmentNameAttribute()
    {
        return $this->department ? $this->department->name : 'Non assigné';
    }

    /**
     * Check if subject has teacher
     */
    public function getHasTeacherAttribute()
    {
        return !is_null($this->teacher_id);
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        if (!$this->is_active) {
            return 'Inactif';
        }
        
        return $this->has_teacher ? 'Assigné' : 'Non assigné';
    }

    /**
     * Get full code with department
     */
    public function getFullCodeAttribute()
    {
        $departmentCode = $this->department ? $this->department->code : 'XXX';
        return $departmentCode . '-' . $this->code;
    }

    /**
     * Get credits description
     */
    public function getCreditsDescriptionAttribute()
    {
        $credits = $this->credits;
        return $credits . ' crédit' . ($credits > 1 ? 's' : '');
    }

    /**
     * Get total students count (through classes)
     */
    public function getTotalStudentsCountAttribute()
    {
        return $this->classes()->withCount('students')->get()->sum('students_count');
    }

    /**
     * Get grades count
     */
    public function getGradesCountAttribute()
    {
        return $this->grades()->count();
    }

    /**
     * Get average grade
     */
    public function getAverageGradeAttribute()
    {
        return $this->grades()->avg('score') ?: 0;
    }

    // Scopes

    /**
     * Scope for active subjects
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for subjects by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope for subjects with teacher
     */
    public function scopeWithTeacher($query)
    {
        return $query->whereNotNull('teacher_id');
    }

    /**
     * Scope for subjects without teacher
     */
    public function scopeWithoutTeacher($query)
    {
        return $query->whereNull('teacher_id');
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Scope for subjects by teacher
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope for subjects by credits
     */
    public function scopeByCredits($query, $credits)
    {
        return $query->where('credits', $credits);
    }

    /**
     * Scope for subjects with minimum credits
     */
    public function scopeMinCredits($query, $minCredits)
    {
        return $query->where('credits', '>=', $minCredits);
    }

    // Méthodes utilitaires

    /**
     * Assign teacher to subject
     */
    public function assignTeacher(Teacher $teacher)
    {
        $this->update(['teacher_id' => $teacher->id]);
        return $this;
    }

    /**
     * Remove teacher from subject
     */
    public function removeTeacher()
    {
        $this->update(['teacher_id' => null]);
        return $this;
    }

    /**
     * Check if subject can be deleted
     */
    public function canBeDeleted()
    {
        // Ne peut pas être supprimé s'il y a des notes ou des plannings
        return !$this->grades()->exists() && !$this->schedules()->exists();
    }

    /**
     * Get subject workload (estimated hours per week)
     */
    public function getWorkloadHours()
    {
        // Estimation: 1.5 heures par crédit par semaine
        return $this->credits * 1.5;
    }

    /**
     * Get formatted description
     */
    public function getFormattedDescriptionAttribute()
    {
        if (!$this->description) {
            return 'Aucune description disponible';
        }
        
        return strlen($this->description) > 100 
            ? substr($this->description, 0, 100) . '...' 
            : $this->description;
    }

    /**
     * Check if subject is assigned to a specific teacher
     */
    public function isAssignedTo(Teacher $teacher)
    {
        return $this->teacher_id === $teacher->id;
    }

    /**
     * Get all available subjects for assignment (without teacher)
     */
    public static function availableForAssignment()
    {
        return static::whereNull('teacher_id')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();
    }

    /**
     * Get subjects by department for dropdown
     */
    public static function byDepartmentForDropdown($departmentId = null)
    {
        $query = static::where('is_active', true);
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        
        return $query->orderBy('name')->pluck('name', 'id');
    }

    /**
     * Get subjects statistics
     */
    public static function getStats()
    {
        return [
            'total' => static::count(),
            'active' => static::where('is_active', true)->count(),
            'with_teacher' => static::whereNotNull('teacher_id')->count(),
            'without_teacher' => static::whereNull('teacher_id')->count(),
            'total_credits' => static::sum('credits'),
        ];
    }
}