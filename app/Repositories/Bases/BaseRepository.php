<?php

namespace App\Repositories\Bases;

use Closure;
use Exception;
use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use DB;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * Default limit
     *
     * @var const
     */
    const DEFAULT_LIMIT = 10;

    /**
     * @var App
     */
    protected $app;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * @var \Closure
     */
    protected $scopeQuery;

    /**
     * Model class name
     *
     * @return string
     */
    abstract protected function model();

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
     * @throws RepositoryException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Get Searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * @throws RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * Query Scope
     *
     * @param \Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope)
    {
        $this->scopeQuery = $scope;
        return $this;
    }

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope()
    {
        $this->scopeQuery = null;
        return $this;
    }
    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope()
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->model = $callback($this->model);
        }
        return $this;
    }

    /**
     * Retrieve data array for populate field select
     *
     * @param string      $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function lists($column, $key = null)
    {
        return $this->makeModel()->lists($column, $key);
    }

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        $this->applyScope();
        if ($this->sortField) {
            $this->orderBy($this->sortField, $this->sortType);
        }
        if ($this->model instanceof Builder) {
            $results = $this->model->get($columns);
        } else {
            $results = $this->model->all($columns);
        }

        $this->resetModel();
        $this->resetScope();
        return $results;
    }

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function first($columns = ['*'])
    {
        $this->applyScope();
        $results = $this->model->first($columns);
        $this->resetModel();
        return $results;
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @param int    $limit
     * @param array  $columns
     * @param string $method
     *
     * @return mixed
     */
    public function paginate($limit = self::DEFAULT_LIMIT, $columns = ['*'], $method = 'paginate')
    {
        $this->applyScope();
        $limit = request()->get('perPage', $limit);
        $results = $this->model->{$method}($limit, $columns);
        $results->appends(request()->query());
        $this->resetModel();
        return $results;
    }

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param null  $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function simplePaginate($limit = null, $columns = ['*'])
    {
        return $this->paginate($limit, $columns, "simplePaginate");
    }

    /**
     * Find data by id
     *
     * @param $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->findOrFail($id, $columns);
        $this->resetModel();
        return $model;
    }

    /**
     * Find data by field and value
     *
     * @param $field
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->where($field, '=', $value)->get($columns);
        $this->resetModel();
        return $model;
    }

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        $this->applyScope();
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
        $model = $this->model->get($columns);
        $this->resetModel();
        return $model;
    }

    /**
     * Find data by multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereIn($field, array $values, $columns = ['*'])
    {
        $model = $this->model->whereIn($field, $values)->get($columns);
        $this->resetModel();
        return $model;
    }

    /**
     * Find data by excluding multiple values in one field
     *
     * @param $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereNotIn($field, array $values, $columns = ['*'])
    {
        $model = $this->model->whereNotIn($field, $values)->get($columns);
        $this->resetModel();
        return $model;
    }

    /**
     * Create a record
     *
     * @param array       $data
     * @param object/bool $result
     *
     * @return object/bool $result
     */
    public function create(array $data, $result = null)
    {
        DB::transaction(
            function () use ($data, &$result) {
                if ($this->sortField) {
                    $maxOrder = $this->getMaxOrder();
                    $data[$this->sortField] = $maxOrder + 1;
                }
                $record = $this->model->create($data);
                $this->resetModel();
                if (!$record) {
                    $result = false;
                } else {
                    $result = $record;
                }
            }
        );
        return $result;
    }

    /**
     * Update a record
     *
     * @param array       $data
     * @param int         $id
     * @param object/bool $result
     *
     * @return object/bool $result Updated record / false if update failed
     */
    public function update(array $data, $id, $result = null)
    {
        DB::transaction(
            function () use ($data, $id, &$result) {
                $this->applyScope();
                $record = $this->find($id);
                $this->resetModel();
                if (!$record->update($data)) {
                    $result = false;
                } else {
                    $result = $record;
                }
            }
        );

        return $result;
    }

    /**
     * Delete a record
     *
     * @param int         $id     Id of record for deleting
     * @param object/bool $result
     *
     * @return object/bool $result Deleted record / false if delete failed
     */
    public function delete($id, $result = null)
    {
        DB::transaction(
            function () use ($id, &$result) {
                $this->applyScope();
                $record = $this->find($id);
                $this->resetModel();
                if ($record && $record->delete()) {
                    if ($this->sortField) {
                        $this->model->where($this->sortField, '>', $record->{$this->sortField})
                            ->decrement($this->sortField, 1);
                    }
                    $result = $record;
                } else {
                    $result = false;
                }
            }
        );

        return $result;
    }

    /**
     * Move record to under another record
     *
     * @param int         $id             Id of record
     * @param int         $priorSiblingId Id of record upper than current record
     * @param object/bool $result
     *
     * @return object/bool $result moved record /false if move failed
     */
    public function move($id, $priorSiblingId, $result = null)
    {
        DB::transaction(
            function () use ($id, $priorSiblingId, &$result) {
                $this->applyScope();
                $record = $this->find($id);
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
                    $this->resetModel();
                    $result = $record;
                } else {
                    $result = false;
                }
            }
        );

        return $result;
    }

    /**
     * Check if entity has relation
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has($relation)
    {
        $this->model = $this->model->has($relation);
        return $this;
    }

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);
        return $this;
    }
    /**
     * Set hidden fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function hidden(array $fields)
    {
        $this->model->setHidden($fields);
        return $this;
    }

    /**
     * Set order by a column
     *
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->query()->orderBy($column, $direction);
        return $this;
    }

    /**
     * Set visible fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function visible(array $fields)
    {
        $this->model->setVisible($fields);
        return $this;
    }

    /**
     * Get order of a record
     *
     * @param int $id
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
