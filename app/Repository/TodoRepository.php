<?php

namespace App\Repository;

use App\Repository\Repository;
use App\Repository\RepositoryInterface;

class TodoRepository extends Repository
{

    public function model()
    {
        return 'App\Models\Todo';
    }

    public function move($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return false;
        }
        return $model;
    }
}
