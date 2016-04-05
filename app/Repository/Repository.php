<?php

namespace App\Repository;

use App\Repository\RepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Container\Container as App;

abstract class Repository implements RepositoryInterface {
	private $app;

	protected $model;

	public function __construct(App $app) {
		$this->app = $app;
		$this->makeModel();
	}

	abstract function model();

	public function makeModel() {
		$model = $this->app->make($this->model());

		if ($model instanceof Model) {
			return $this->model = $model;
		}
	}

	public function all() {
		return $this->model->all();
	}

	public function get($id) {
		$model = $this->model->find($id);
        if (!$model) {
            return new JsonResponse(['message' => 'Not found'], 404);
        } else {
            return $model;
        }
	}

	public function create(array $data) {
		return $this->model->create($data);
	}

	public function update(array $data, $id) {
		die("abv");
		$model = $this->model->find($id);
        if (!$model) {
            return new JsonResponse(['message' => 'Not found'], 404);
        }
        $model->update($data);
        return $model;
	}

	public function delete($id) {
		$model = $this->model->find($id);
        if (!$model) {
            return new JsonResponse(['message' => 'Not found'], 404);
        }
        return $model->delete();
	}

	public function move($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return new JsonResponse(['message' => 'Not found'], 404);
        }
        return $model;
    }
}