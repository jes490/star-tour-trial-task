<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskResult extends Model
{
    //
    protected $table = 'task_results';

    protected $fillable = ['result'];
}
