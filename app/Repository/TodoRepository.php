<?php

namespace App\Repository;

use App\Repository\Repository;
use App\Repository\RepositoryInterface;

class TodoRepository extends Repository {
	function model() {
		return 'App\Models\Todo';
	}
}