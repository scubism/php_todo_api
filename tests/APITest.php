<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

class APITest extends TestCase {
    // use DatabaseMigrations;

    public function testAllTodos() {
        $this->json('GET', '/v1/todos')
            ->seeJson([
                'id' => 3
            ]);
    }

    public function testViewTodo() {
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
                ’todo_groups_id' => '1'])
            ->seeJson([
                'title' => 'Test Todo 1 Updated',
                'color' => 'white', 
                'todo_groups_id' => 1
            ]);
    }

    public function testDeleteTodo() {
        $response = $this->call('DELETE', '/v1/todos/0');
        $this->assertEquals(404, $response->status());

        $this->json('DELETE', '/v1/todos/0')
            ->seeJson([
                'error' => 'Not found.'
            ]);
        $this->json('DELETE', '/v1/todos/110')
            ->seeJson([
                'title' => 'Test Todo 1',
                'color' => 'black', 
                'todo_groups_id' => 1
            ]);
        $this->json('GET', '/v1/todos/110')
            ->dontSeeJson(['id' => 110]);
    }

    public function testMoveTodo() {
        $this->json('POST', '/v1/todos/0/move', ['prior_sibling_id' => 2])
            ->seeJson([
                'error' => 'Not found.'
            ]);

        $this->json('POST', '/v1/todos/18/move', ['prior_sibling_id' => 0])
            ->seeJson([
                'id' => 18,
                'sort_order' => 1
            ]);
        $responseTodo3 = $this->call('GET', '/v1/todos/3');
        $jsonTodo3 = json_decode($responseTodo3->getContent());
		
        $responseTodo18 = $this->call('GET', '/v1/todos/18');
        $jsonTodo18 = json_decode($responseTodo18->getContent());

        $sort_orderTodo18 = $jsonTodo18->{'sort_order'};

        $sort_orderTodo3 = 0;
        if (array_key_exists('sort_order', $jsonTodo3)) {
            $sort_orderTodo3 = $jsonTodo3->{'sort_order'};
        }
        if ($sort_orderTodo18 > $sort_orderTodo3) {
            $sort_orderTodo3++;	
        }

        $this->json('POST', '/v1/todos/18/move', ['prior_sibling_id' => 3])
            ->seeJson([
                'id' => 18,
                'sort_order' => $sort_orderTodo3
            ]);
    }
}
