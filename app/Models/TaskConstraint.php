<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskConstraint extends Model
{
    //
    protected $table = 'task_constraints';

    protected $fillable = ['constraint'];
}
