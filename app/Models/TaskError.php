<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskError extends Model
{
    //
    protected $table = 'task_errors';

    protected $fillable = ['message', 'HTTPCode'];
}
