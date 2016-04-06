<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class TodoRepository extends Repository
{

    /**
     * Field for sorting
     *
     * @var string
     */
    public $sortField = 'sort_order';

    /**
     * Type of sorting (asc/desc)
     *
     * @var string
     */
    public $sortType = 'asc';

    /**
     * Model class name
     *
     * @return string
     */
    public function model()
    {
        return \App\Models\Todo::class;
    }
}
