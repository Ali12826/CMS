<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * Table Configuration
     */
    protected $table = 'task_info';
    protected $primaryKey = 'task_id'; // Crucial: Tells Laravel to use task_id instead of id
    public $timestamps = false;        // Set to true if you have created_at/updated_at columns

    /**
     * Mass Assignable Attributes
     */
    protected $fillable = [
        't_title',
        'dept_id',
        'location',
        't_description',
        't_start_time',
        't_end_time',
        't_user_id',
        'assigned_by',
        'status',
    ];

    /**
     * Type Casting
     */
    protected $casts = [
        't_start_time' => 'datetime',
        't_end_time'   => 'datetime',
        'status'       => 'integer',
        'dept_id'      => 'integer',
        't_user_id'    => 'integer',
        'assigned_by'  => 'integer',
    ];

    /**
     * Constants for Status
     */
    public const STATUS_INCOMPLETE = 0;
    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_COMPLETED = 2;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Employee who received the task (Assigned To)
     */
    public function assignedTo()
    {
        return $this->belongsTo(Admin::class, 't_user_id', 'user_id');
    }

    /**
     * Admin/User who assigned the task (Assigned By)
     */
    public function assignedBy()
    {
        return $this->belongsTo(Admin::class, 'assigned_by', 'user_id');
    }

    /**
     * Department relationship
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get human-readable status text.
     * Usage: $task->status_text
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED   => 'Completed',
            default                  => 'Incomplete',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes (Query Filters)
    |--------------------------------------------------------------------------
    */

    /**
     * Filter tasks for a specific user (either assigned TO or BY them)
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('t_user_id', $userId)
              ->orWhere('assigned_by', $userId);
        });
    }

    /**
     * Filter tasks for a specific department
     */
    public function scopeForDept($query, int $deptId)
    {
        return $query->where('dept_id', $deptId);
    }
}
