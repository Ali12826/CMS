<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'tbl_admin';
    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = [
        'fullname',
        'username',
        'contact',
        'password',
        'temp_password',
        'user_role',
        'dept_id'
    ];

    // Disable default bcrypt hashing
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = md5($value);
    }
}
