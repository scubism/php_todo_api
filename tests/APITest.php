<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Models\Todo;
use Illuminate\Support\Facades\Artisan as Artisan;

class APITest extends TestCase {
    use DatabaseMigrations;

    public function testAllTodos() {
        \App\Models\Todo::create(['title' => 'Test Todo', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '1']);
        \App\Models\Todo::create(['title' => 'Test Todo 2', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '2']);
        \App\Models\Todo::create(['title' => 'Test Todo 3', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '3']);
        $this->json('GET', '/v1/todos')
            ->seeJson([
                'id' => 3
            ]);
    }

    public function testViewTodo() {
    	\App\Models\Todo::create(['title' => 'Test Todo', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '1']);
        \App\Models\Todo::create(['title' => 'Test Todo 2', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '2']);
        \App\Models\Todo::create(['title' => 'Test Todo 3', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '3']);
        $this->json('GET', '/v1/todos/0')
            ->seeJson([
                'error' => 'Not found.'
            ]);

        $this->json('GET', '/v1/todos/3')
            ->seeJson([
                'id' => 3
            ]);
    }

    public function testCreateTodo() {
        $this->json('POST', '/v1/todos', ['title' => 'Test Todo 1', 
                'duedate' => '2016-04-20 10:42', 
                'color' => 'black', 
                'todo_groups_id' => '1'])
            ->seeJson([
                'title' => 'Test Todo 1',
                'color' => 'black', 
                'todo_groups_id' => 1
            ]);

        $response = $this->call('POST', '/v1/todos', ['title' => 'Test Todo 1', 
            'duedate' => '2016-04-20 10:42', 
            'color' => 'black', 
            'todo_groups_id' => '1']);
        $this->assertEquals(200, $response->status());
    }

    public function testUpdateTodo() {
    	\App\Models\Todo::create(['title' => 'Test Todo', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '1']);
        \App\Models\Todo::create(['title' => 'Test Todo 2', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '2']);
        \App\Models\Todo::create(['title' => 'Test Todo 3', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '3']);

        $response = $this->call('PUT', '/v1/todos/0', [
            'title' => 'Test Todo 0',
            'duedate' => '2016-04-20 10:42', 
            'color' => 'black', 
            'todo_groups_id' => '1'
        ]);
        $this->assertEquals(404, $response->status());
		
        $this->json('PUT', '/v1/todos/0', ['title' => 'Test Todo 1 Updated', 
            'duedate' => '2016-04-20 10:42', 
            'color' => 'black', 
            'todo_groups_id' => '1'])
            ->seeJson([
                'error' => 'Not found.'
            ]);

        $this->json('PUT', '/v1/todos/3', ['title' => 'Test Todo 1 Updated', 
                'duedate' => '2016-04-20 10:42', 
                'color' => 'white', 
                'todo_groups_id' => '1'])
            ->seeJson([
                'title' => 'Test Todo 1 Updated',
                'color' => 'white', 
                'todo_groups_id' => '1'
            ]);
    }

    public function testDeleteTodo() {
    	\App\Models\Todo::create(['title' => 'Test Todo', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '1']);
        \App\Models\Todo::create(['title' => 'Test Todo 2', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '2']);
        \App\Models\Todo::create(['title' => 'Test Todo 3', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '3']);
        $response = $this->call('DELETE', '/v1/todos/0');
        $this->assertEquals(404, $response->status());

        $this->json('DELETE', '/v1/todos/0')
            ->seeJson([
                'error' => 'Not found.'
            ]);
        $this->json('DELETE', '/v1/todos/3')
            ->seeJson([
                'title' => 'Test Todo 3',
                'color' => 'black', 
                'todo_groups_id' => '1'
            ]);
        $this->json('GET', '/v1/todos/3')
            ->dontSeeJson(['id' => 3]);
    }

    public function testMoveTodo() {
    	\App\Models\Todo::create(['title' => 'Test Todo', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '1']);
        \App\Models\Todo::create(['title' => 'Test Todo 2', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '2']);
        \App\Models\Todo::create(['title' => 'Test Todo 3', 'todo_groups_id' => '1', 'color' => 'black', 'sort_order' => '3']);
        $this->json('POST', '/v1/todos/0/move', ['prior_sibling_id' => 2])
            ->seeJson([
                'error' => 'Not found.'
            ]);

        $this->json('POST', '/v1/todos/3/move', ['prior_sibling_id' => 0])
            ->seeJson([
                'id' => 3,
                'sort_order' => 1
            ]);
        $responseTodo2 = $this->call('GET', '/v1/todos/2');
        $jsonTodo2 = json_decode($responseTodo2->getContent());
		
        $responseTodo3 = $this->call('GET', '/v1/todos/3');
        $jsonTodo3 = json_decode($responseTodo3->getContent());

        $sort_orderTodo3 = $jsonTodo3->{'sort_order'};

        $sort_orderTodo2 = 0;
        if (array_key_exists('sort_order', $jsonTodo3)) {
            $sort_orderTodo2 = $jsonTodo2->{'sort_order'};
        }
        if ($sort_orderTodo3 > $sort_orderTodo2) {
            $sort_orderTodo2++;	
        }

        $this->json('POST', '/v1/todos/3/move', ['prior_sibling_id' => 2])
            ->seeJson([
                'id' => 3,
                'sort_order' => $sort_orderTodo2
            ]);
    }
}
