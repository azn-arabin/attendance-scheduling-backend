<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     * (Using 'classes' to avoid conflict with the PHP keyword 'class'.)
     */
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'batch_id', 'instructor_id', 'topic', 'start_time', 'duration'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'start_time' => 'datetime',
    ];

    /**
     * The batch this class belongs to.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * The instructor (User) teaching this class.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Attendance records for this class.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    /**
     * Students attending this class (through the attendances table).
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attendances', 'class_id', 'student_id');
    }

}
