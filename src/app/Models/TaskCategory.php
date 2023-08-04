<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
