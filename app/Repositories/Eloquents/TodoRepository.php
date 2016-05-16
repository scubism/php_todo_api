<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Bases\BaseRepository;
use App\Repositories\Interfaces\RepositoryInterface;

class TodoRepository extends BaseRepository
{

    /**
     * Field for sorting
     *
     * @var string
     */
    protected $sortField = 'sort_order';

    /**
     * Type of sorting (asc/desc)
     *
     * @var string
     */
    protected $sortType = 'asc';

    /**
     * Searchable fields
     *
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'due_date',
        'color',
        'todo_groups_id',
        'sort_order',
        'marked'
    ];

    /**
     * Model class name
     *
     * @return string
     */
    protected function model()
    {
        return \App\DataAccess\Eloquent\Todo::class;
    }
}
