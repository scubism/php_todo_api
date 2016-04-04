<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodosController extends Controller
{
    // TODO Add todos repository

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
            'message' => 'Hello from API Index'
        ];
        return $response;
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
        return $todo;
    }

    public function createTodo(Request $request)
    {
        // TODO Check exist
        $todo = new Todo;
        $todo->title = $request->title;
        $todo->duedate = $request->duedate;
        $todo->color = $request->color;
        $todo->todo_groups_id = $request->todo_groups_id;

        return $todo->save();
    }

    public function updateTodo($id, Request $request)
    {
        // TODO Check exist
        $todo = Todo::find($id);
        $todo->update($request);
        return $todo;
    }

    public function deleteTodo($id)
    {
        // TODO Check exist
        $todo = Todo::find($id);
        return $todo->delete();;
    }

    public function moveTodo($id)
    {
        // TODO Implement this action
        $todo = Todo::find($id);
        return $todo;
    }
}
