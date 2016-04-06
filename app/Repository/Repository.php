<?php

namespace App\Repository;

use App\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Container\Container as App;

abstract class Repository implements RepositoryInterface
{

    private $app;

    protected $model;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    abstract function model();

    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if ($model instanceof Model) {
            return $this->model = $model;
        }
    }

    public function all()
    {
        return $this->model->all();
    }

    public function get($id)
    {
        $record = $this->model->find($id);
        if ($record) {
            return $record;
        }
        return false;
    }

    public function create(array $data)
    {
        $record = $this->model->create($data);
        if ($record) {
            return $record;
        }
        return false;
    }

    public function update(array $data, $id)
    {
        $record = $this->model->find($id);
        if ($record && $record->update($data)) {
            return $record;
        }
        return false;
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if ($record && $record->delete()) {
            return $record;
        }
        return false;
    }
}
