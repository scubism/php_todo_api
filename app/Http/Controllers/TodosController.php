<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repository\TodoRepository;

class TodosController extends Controller
{
    // TODO Add todos repository
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
        // $result = Todo::all();
        // return $result;
        return $this->todoRepo->all();
    }

    public function viewTodo($id)
    {
        return $this->todoRepo->get($id);
    }

    public function createTodo(Request $request)
    {
        // TODO Check parameter null
        $this->validate($request, [
            'title' => 'required',
            'todo_groups_id' => 'required'
        ]);

        $data = array('title' => $request->input('title'), 
            'duedate' => $request->input('duedate', null), 
            'color' => $request->input('color', null), 
            'todo_groups_id' => $request->todo_groups_id);
        return $this->todoRepo->create($data);
    }

    public function updateTodo($id, Request $request)
    {
        // TODO Check exist
        // TODO Check parameter null
        // $todo = Todo::find($id);
        // if (!$todo) {
        //     return new JsonResponse(['message' => 'Not found'], 404);
        // }
        // $todo->update($request);
        // return $todo;
        $this->validate($request, [
            'title' => 'required',
            'todo_groups_id' => 'required'
        ]);
        $data = array('title' => $request->input('title'), 
            'duedate' => $request->input('duedate', null), 
            'color' => $request->input('color', null), 
            'todo_groups_id' => $request->todo_groups_id);

        return $this->todoRepo->update($data, $id);
    }

    public function deleteTodo($id)
    {
        // TODO Check exist
        // $todo = Todo::find($id);
        // if (!$todo) {
        //     return new JsonResponse(['message' => 'Not found'], 404);
        // }
        // return $todo->delete();
        return $this->todoRepo->delete($id);
    }

    public function moveTodo($id)
    {
        // TODO Implement this action
        // with transaction
        // $todo = Todo::find($id);
        // if (!$todo) {
        //     return new JsonResponse(['message' => 'Not found'], 404);
        // }
        // return $todo;
        return $this->todoRepo->move($id);
    }
}
