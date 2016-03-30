<?php

namespace App\Http\Controllers;

class APIController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        echo "Hello from API Index";
    }

    public function getTodos() {
        echo "Getting todos from todos table";
        $result = DB::select("SELECT * FROM todos");
        // echo $result;
        return $result;
    }
    //

    public function getTodo($id) {
        echo "Getting todo by id";
        $result = DB::select("SELECT * FROM todos WHERE id = ?", $id);
        return $result;
    }

    
}