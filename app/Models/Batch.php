<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected  $fillable = [
        'name',
        'description',
    ];

    /**
     * Instructors teaching this batch.
     */
    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'batch_instructor', 'batch_id', 'instructor_id');
    }

    /**
     * Students enrolled in this batch.
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }

    /**
     * Classes scheduled for this batch.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }
}
