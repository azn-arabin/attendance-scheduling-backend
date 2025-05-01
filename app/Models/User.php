<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name', 'email', 'phone', 'gender', 'role', 'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Add these 2 methods as required by JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Typically the user ID
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The batches where the user is enrolled as a student.
     */
    public function batchesAsStudent(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class, 'batch_student', 'student_id', 'batch_id');
    }

    /**
     * The batches where the user is assigned as an instructor.
     */
    public function batchesAsInstructor(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class, 'batch_instructor', 'instructor_id', 'batch_id');
    }

    /**
     * Attendance records for the user (as a student).
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    /**
     * Determine if the user has a 'student' role.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Determine if the user has an 'instructor' role.
     */
    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    /**
     * Determine if the user has an 'admin' role.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
