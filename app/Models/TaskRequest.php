<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskRequest extends Model
{
    //
    protected $table = 'task_requests';

    protected $fillable = ['url', 'status'];

    public function results()
    {
        return $this->hasMany(TaskResult::class);
    }

    public function constraints()
    {
        return $this->hasMany(TaskConstraint::class);
    }

    public function errors()
    {
        return $this->hasMany(TaskError::class);
    }


}
