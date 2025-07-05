<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    // Accesseurs
    public function getClassesCountAttribute()
    {
        return $this->classes()->count();
    }

    public function getActiveClassesCountAttribute()
    {
        return $this->classes()->where('is_active', true)->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
