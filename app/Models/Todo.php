<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    protected $table = 'todos';

    use SoftDeletes;

    /**
     * Get the group that owns this todo
     */
    public function group()
    {
        return $this->belongsTo('App\TodoGroup', 'todo_groups_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'duedate',
        'color',
        'todo_groups_id',
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
