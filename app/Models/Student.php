<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'student_number',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'address',
        'enrollment_date',
        'course',
        'year_level',
        'gpa',
        'status',
        'remarks',
        'profile_photo',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth'     => 'date',
        'enrollment_date'   => 'date',
        'gpa'               => 'decimal:2',
        'email_verified_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFormattedStudentNumberAttribute(): string
    {
        return 'STU-' . $this->student_number;
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('first_name',      'LIKE', "%{$term}%")
                  ->orWhere('last_name',      'LIKE', "%{$term}%")
                  ->orWhere('student_number', 'LIKE', "%{$term}%")
                  ->orWhere('email',          'LIKE', "%{$term}%")
                  ->orWhere('course',         'LIKE', "%{$term}%");
        });
    }
}