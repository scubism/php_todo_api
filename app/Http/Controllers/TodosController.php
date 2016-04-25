<?php

namespace App\Http\Controllers;

use App\Repositories\TodoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodosController extends Controller
{
    // TODO Add Middleware to check todo exist
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

        $this->middleware('check_exist:\App\Models\Todo', [
            'only' => [
                'viewTodo', 'updateTodo', 'deleteTodo', 'moveTodo'
            ]
        ]);
    }

    public function index()
    {
        $response = [
            'message' => 'Hello from API Index',
        ];
        return new JsonResponse($response, 200);
    }

    public function indexTodos()
    {
        return $this->todoRepo->all();
    }

    public function viewTodo($id)
    {
        $result = $this->todoRepo->get($id);
        if ($result) {
            return $result;
        }
        return response(['error' => 'Couldn\'t get Todo'], 422);
    }

    public function createTodo(Request $request)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        $data = [
            'title' => $request->input('title'),
            'due_date' => $request->input('due_date', null),
            'color' => $request->input('color', null),
            'todo_groups_id' => $request->input('todo_groups_id'),
            'marked' => $request->input('marked', 0)
        ];

        $created = $this->todoRepo->create($data);
        if ($created) {
            return $created;
        }
        return response(['error' => 'Couldn\'t create Todo'], 422);
    }

    public function updateTodo($id, Request $request)
    {
        // TODO Check exist in middleware
        $this->validate($request, [
            'title' => 'required'
        ]);

        $data = [
            'title' => $request->input('title'),
            'due_date' => $request->input('due_date', null),
            'color' => $request->input('color', null),
            'marked' => $request->input('marked', 0)
        ];

        $updated = $this->todoRepo->update($data, $id);
        if ($updated) {
            return $updated;
        }
        return response(['error' => 'Couldn\'t update'], 422);
    }

    public function deleteTodo($id)
    {
        $deleted = $this->todoRepo->delete($id);
        if ($deleted) {
            return $deleted;
        }
        return response(['error' => 'Couldn\'t delete'], 422);
    }

    public function moveTodo($id, Request $request)
    {
        $moved = $this->todoRepo->move($id, $request->input('prior_sibling_id', ''));
        if ($moved) {
            return $moved;
        }
        return response(['error' => 'Couldn\'t move'], 422);
    }
}
