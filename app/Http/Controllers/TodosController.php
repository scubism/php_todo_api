<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodosController extends Controller
{
    // TODO Add todos repository
    // TODO Add Middleware to check todo exist

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Construct
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
        // TODO Check exist
        $result = Todo::all();
        return $result;
    }

    public function viewTodo($id)
    {
        // TODO Check exist
        $todo = Todo::find($id);
        if (!$todo) {
            return new JsonResponse(['message' => 'Not found'], 404);
        } else {
            return $todo;
        }
    }

    public function createTodo(Request $request)
    {
        // TODO Check exist
        // TODO Check parameter null
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'todo_groups_id' => 'required',
        ]);
        $todo = new Todo();
        $todo->title = $request->title;
        $todo->color = $request->color;
        $todo->duedate = $request->duedate;
        $todo->todo_groups_id = $request->todo_groups_id;
        return $todo->save();
    }

    public function updateTodo($id, Request $request)
    {
        // TODO Check exist
        // TODO Check parameter null
        $todo = Todo::find($id);
        if (!$todo) {
            return new JsonResponse(['message' => 'Not found'], 404);
        }
        $todo->update($request);
        return $todo;
    }

    public function deleteTodo($id)
    {
        // TODO Check exist
        $todo = Todo::find($id);
        if (!$todo) {
            return new JsonResponse(['message' => 'Not found'], 404);
        }
        return $todo->delete();
    }

    public function moveTodo($id)
    {
        // TODO Implement this action
        // with transaction
        $todo = Todo::find($id);
        if (!$todo) {
            return new JsonResponse(['message' => 'Not found'], 404);
        }
        return $todo;
    }
}
