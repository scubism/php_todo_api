<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;

class TodoRepository extends BaseRepository
{

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
    public function model()
    {
        return \App\DataAccess\Eloquent\Todo::class;
    }

    /**
     * Get all with sorted
     *
     * @param  array  $columns
     *
     * @return object
     */
    public function all($columns = ['*'])
    {
        return $this->model::sorted()->get($columns);
    }

    /**
     * Delete todo
     *
     * @param  int $id
     * @return object/bool
     */
    public function delete($id)
    {
        $todo = $this->find($id);
        if (parent::delete($id)) {
            return $todo;
        }
        return false;
    }

    /**
     * Move Todo After another Todo
     *
     * @param  int $id
     * @param  int $priorSiblingId
     *
     * @return object
     */
    public function move($id, $priorSiblingId)
    {
        $todo = $this->find($id);
        $beforeOrder = $todo->sort_order;
        if (empty($priorSiblingId)) {
            $priorSibling = $this->model::sorted()->first();
            $todo->moveBefore($priorSibling);
        } else {
            $priorSibling = $this->find($priorSiblingId);
            $todo->moveAfter($priorSibling);
        }
        $afterOrder = $todo->sort_order;
        if (intval($beforeOrder) === intval($afterOrder)) {
            return false;
        }
        return $todo;
    }
}
