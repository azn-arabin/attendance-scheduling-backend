<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{

    /**
     * The table associated with the model.
     * (Using 'classes' to avoid conflict with the PHP keyword 'class'.)
     */
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'topic',
        'start_time',
        'duration',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'start_time' => 'datetime',
    ];

    /**
     * Batches that this class (session) belongs to.
     */
    public function batches(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class, 'class_batch', 'class_id', 'batch_id');
    }

    /**
     * Attendance records for this class.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    /**
     * Determine if the class is currently ongoing.
     */
    public function isOngoing(): bool
    {
        if (! $this->start_time) {
            return false;
        }

        $start = $this->start_time;
        $end = $start->copy()->addMinutes($this->duration);

        return now()->between($start, $end);
    }
}
