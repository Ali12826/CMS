<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    // 1. Table Name
    protected $table = 'tbl_admin';

    // 2. Primary Key (CRITICAL: Must be 'user_id')
    protected $primaryKey = 'user_id';

    // 3. Timestamps (False if you don't have created_at/updated_at columns)
    public $timestamps = false;

    // 4. Fillable Fields
    protected $fillable = [
        'fullname',
        'username',
        'contact',
        'dept_id',
        'password',
        'temp_password',
        'user_role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // =========================================================
    // THIS IS THE MISSING PART CAUSING YOUR ERROR
    // =========================================================
    public function department()
    {
        // This tells Laravel: "This Admin belongs to a Department"
        // It joins 'tbl_admin.dept_id' with 'departments.dept_id'
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }
}
