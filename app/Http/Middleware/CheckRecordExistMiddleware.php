<?php

namespace App\Http\Middleware;

use Closure;
use App\Repositories\TodoRepository;
use Illuminate\Container\Container as App;

class CheckRecordExistMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $class)
    {
        # /v1/todos/{id}
        $model = new $class;
        $id = $request->segment(3);
        if (!$id || !$model->find($id)) {
            return response(['message' => 'Couldn\'t find the todo'], 500);
        }
        return $next($request);
    }
}
