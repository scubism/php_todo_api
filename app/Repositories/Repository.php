<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
use DB;

abstract class Repository implements RepositoryInterface
{

    protected $app;

    protected $model;

    abstract function model();

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if ($model instanceof Model) {
            $this->model = $model;
            return $this->model;
        }
    }

    public function all()
    {
        $model = $this->model;
        if ($this->sortField) {
            $model = $this->model->orderBy($this->sortField, $this->sortType);
        }
        return $model->get();
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
        DB::beginTransaction();
        try {
            if ($this->sortField) {
                $maxOrder = $this->getMaxOrder();
                $data[$this->sortField] = $maxOrder + 1;
            }
            $record = $this->model->create($data);
            if ($record) {
                DB::commit();
                return $record;
            }
        } catch (Exception $e) {
            DB::rollBack();
        }
        return false;
    }

    public function update(array $data, $id)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->find($id);
            if ($record && $record->update($data)) {
                DB::commit();
                return $record;
            }
        } catch (Exception $e) {
            DB::rollBack();
        }
        return false;
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->find($id);
            if ($record && $record->delete()) {
                if ($this->sortField) {
                    $this->model->where($this->sortField, '>', $record->{$this->sortField})
                        ->decrement($this->sortField, 1);
                }
                DB::commit();
                return $record;
            }
        } catch (Exception $e) {
            DB::rollBack();
        }
        return false;
    }

    /**
     * Move Todo
     *
     * @param  int $id Todo ID
     * @param  int $priorSiblingId Id of Todo upper than current todo
     *
     * @return object
     */
    public function move($id, $priorSiblingId)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->find($id);
            if ($record) {
                // Get prior sibling record's order
                $priorOrder = $this->getOrder($priorSiblingId);
                $currentOrder = $record->{$this->sortField};
                if ($currentOrder > $priorOrder) {
                    $record->{$this->sortField} = $priorOrder + 1;
                    $record->save();
                    $this->model->where($this->sortField, '<', $currentOrder)
                        ->where($this->sortField, '>', $priorOrder)
                        ->where('id', '!=', $id)
                        ->increment($this->sortField, 1);
                } elseif ($currentOrder < $priorOrder) {
                    $record->{$this->sortField} = $priorOrder;
                    $record->save();
                    $this->model->where($this->sortField, '>', $currentOrder)
                        ->where($this->sortField, '<=', $priorOrder)
                        ->where('id', '!=', $id)
                        ->decrement($this->sortField, 1);
                }
                DB::commit();
                return $record;
            }
        } catch (Exception $e) {
            DB::rollBack();
        }
        return false;
    }

    public function getOrder($id)
    {
        $order = $this->model->find($id);
        if ($order) {
            return $order->{$this->sortField};
        }
        return 0;
    }

    /**
     * Get max order by sorted field
     * @return int
     */
    public function getMaxOrder()
    {
        $maxOrder = $this->model->max($this->sortField);
        if ($maxOrder) {
            return $maxOrder;
        }
        return 0;
    }
}
