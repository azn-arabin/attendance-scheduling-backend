<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     * Users (students) associated with this batch.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'batch_student', 'batch_id', 'student_id');
    }

    /**
     * Users (instructors) associated with this batch.
     */
    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'batch_instructor', 'batch_id', 'instructor_id');
    }

    /**
     * Classes (sessions) that are part of this batch.
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_batch', 'batch_id', 'class_id');
    }
}
