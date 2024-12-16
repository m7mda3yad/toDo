<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Entities\Task;
class Category extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['name'];

    // Define the relationship with the Task model
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

}
