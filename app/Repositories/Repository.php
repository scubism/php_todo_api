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

    /**
     * Construct
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Create model
     *
     * @return Model
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if ($model instanceof Model) {
            $this->model = $model;
            return $this->model;
        }
    }

    /**
     * Get all records
     *
     * @return array
     */
    public function all()
    {
        $model = $this->model;
        if ($this->sortField) {
            $model = $model->orderBy($this->sortField, $this->sortType);
        }
        return $model->get();
    }

    /**
     * Get detail of a record
     *
     * @param  int $id
     *
     * @return object/bool
     */
    public function get($id)
    {
        $record = $this->model->findOrFail($id);
        return $record;
    }

    /**
     * Create a record
     *
     * @param  array  $data
     *
     * @return object/bool
     */
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

    /**
     * Update a record
     *
     * @param  array $data
     * @param  int $id
     *
     * @return object/bool Updated record / false if update failed
     */
    public function update(array $data, $id)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
            if ($record->update($data)) {
                DB::commit();
                return $record;
            }
        } catch (Exception $e) {
            DB::rollBack();
        }
        return false;
    }

    /**
     * Delete a record
     *
     * @param  int $id Id of record for deleting
     *
     * @return object/bool Deleted record / false if delete failed
     */
    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
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
     * Move record to under another record
     *
     * @param  int $id Id of record
     * @param  int $priorSiblingId Id of record upper than current record
     *
     * @return object/bool moved record /false if move failed
     */
    public function move($id, $priorSiblingId)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
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

    /**
     * Get order of a record
     *
     * @param  int $id
     *
     * @return int
     */
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
     *
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
