<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    protected $table = 'task_types';
    protected $primaryKey = 'type_id';
    public $timestamps = false;

    protected $fillable = ['dept_id', 'task_name'];
}
