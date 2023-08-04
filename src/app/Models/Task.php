<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $fillable = [
    'title',
    'content',
    'date',
    'finished',
    'published',
    'user_id',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function categories()
  {
    return $this->belongsToMany(Category::class, 'task_categories')
      ->as('task_categories')
      ->whereNull('task_categories.deleted_at')
      ->whereNull('categories.deleted_at')
      ->withPivot('id');
  }
}
