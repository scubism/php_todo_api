<?php

namespace App\Http\Controllers;

use App\Repositories\TodoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodosController extends Controller
{
    private $todoRepo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TodoRepository $todoRepo)
    {
        // Construct
        $this->todoRepo = $todoRepo;
    }

    /**
     * Action for route GET /
     * For API working check
     *
     * @return JsonResponse
     */
    public function index()
    {
        $response = [
            'message' => 'Hello from API Index',
        ];
        return new JsonResponse($response, 200);
    }

    /**
     * Action for route GET /v1/todos/
     * Get all todo from database
     *
     * @return array
     */
    public function indexTodos()
    {
        return $this->todoRepo->all();
    }

    /**
     * Action for route GET /v1/todos/{id}
     * Get only one todo by its id
     *
     * @param  int $id Todo Id
     *
     * @return object
     */
    public function viewTodo($id)
    {
        return $this->todoRepo->find($id);
    }

    /**
     * Action for route POST /v1/todos
     *
     * @param  Request $request New Todo data
     *
     * @return object/json
     */
    public function createTodo(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:16'
        ]);

        $data = [
            'title' => $request->input('title'),
            'due_date' => $request->input('due_date', null),
            'color' => $request->input('color', null),
            'todo_groups_id' => $request->input('todo_groups_id'),
            'marked' => $request->input('marked', 0)
        ];

        $created = $this->todoRepo->create($data);
        if (!$created) {
            return response(['message' => 'Couldn\'t create Todo'], 400);
        }
        return $created;
    }

    /**
     * Action for PUT /v1/todos/{id}
     * Update a todo
     *
     * @param  int  $id      Todo Id
     * @param  Request $request New Todo data
     *
     * @return object/json
     */
    public function updateTodo($id, Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:16'
        ]);

        $data = [
            'title' => $request->input('title'),
            'due_date' => $request->input('due_date', null),
            'color' => $request->input('color', null),
            'marked' => $request->input('marked', 0)
        ];

        $updated = $this->todoRepo->update($data, $id);
        if (!$updated) {
            return response(['message' => 'Couldn\'t update the Todo'], 400);
        }
        return $updated;
    }

    /**
     * Action for DELETE /v1/todos/{id}
     * Delete a todo
     *
     * @param  int $id Todo Id
     *
     * @return object/json
     */
    public function deleteTodo($id)
    {
        $deleted = $this->todoRepo->delete($id);
        if (!$deleted) {
            return response(['message' => 'Couldn\'t delete the Todo'], 400);
        }
        return $deleted;
    }

    /**
     * Action for POST /v1/todos/{id}/move
     * Move a todo right after another todo
     *
     * @param  int  $id      Todo Id
     * @param  Request $request New Todo Data
     *
     * @return object/json
     */
    public function moveTodo($id, Request $request)
    {
        $priorSiblingId = $request->input('prior_sibling_id', '');
        $moved = $this->todoRepo->move($id, $priorSiblingId);
        if (!$moved) {
            return response(['message' => 'Couldn\'t move the Todo'], 400);
        }
        return $moved;
    }
}
