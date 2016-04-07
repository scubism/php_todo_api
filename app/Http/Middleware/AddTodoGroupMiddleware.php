<?php

namespace App\Http\Middleware;

use Closure;

class AddTodoGroupMiddleware
{
    const DEFAULT_TODOGROUP_ID = 1;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->merge(['todo_groups_id' => self::DEFAULT_TODOGROUP_ID]);

        return $next($request);
    }
}
