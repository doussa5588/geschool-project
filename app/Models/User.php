<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'date_of_birth',
        'profile_photo',
        'is_active',
        'gender',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    // Relations
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    // Accesseurs
    
    /**
     * Get the user's full name (alias pour name)
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get the user's age from date of birth
     */
    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return Carbon::parse($this->date_of_birth)->age;
    }

    /**
     * Get the user's initials for avatar
     */
    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        $initials = '';
        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }
        return substr($initials, 0, 2);
    }

    /**
     * Get formatted birth date
     */
    public function getFormattedBirthDateAttribute()
    {
        if (!$this->date_of_birth) {
            return 'Non définie';
        }
        return Carbon::parse($this->date_of_birth)->format('d/m/Y');
    }

    /**
     * Check if user has profile photo
     */
    public function getHasPhotoAttribute()
    {
        return !empty($this->profile_photo);
    }

    /**
     * Get profile photo URL or generate avatar
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        
        // Generate avatar URL using UI Avatars
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=059669&color=fff&size=200";
    }

    /**
     * Get user's role names
     */
    public function getRoleNamesAttribute()
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Check if user is active
     */
    public function getIsActiveStatusAttribute()
    {
        return $this->is_active;
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute()
    {
        if (!$this->phone) {
            return 'Non défini';
        }
        
        // Format for Senegalese phone numbers
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (strlen($phone) === 9 && substr($phone, 0, 1) === '7') {
            return '+221 ' . substr($phone, 0, 2) . ' ' . substr($phone, 2, 3) . ' ' . substr($phone, 5, 4);
        }
        
        return $this->phone;
    }

    // Scopes

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for users with specific role
     */
    public function scopeWithRole($query, $role)
    {
        return $query->whereHas('roles', function($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
    }
}