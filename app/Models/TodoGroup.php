<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TodoGroup extends Model
{
    protected $table = 'todo_groups';

    use SoftDeletes;

    /**
     * Get all todos of current group
     */
    public function todos()
    {
        return $this->hasMany('App\Todo');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at', 'created_at', 'updated_at'
    ];

}
