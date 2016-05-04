<?php

use App\Models\Todo;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan as Artisan;

class APITest extends TestCase {
    use DatabaseMigrations;

    const DEFAULT_TODO_NUMBER = 10;

    public function testListTodosWithData()
    {
        factory(App\Models\Todo::class, APITest::DEFAULT_TODO_NUMBER)->create();
        $this->call('GET', '/v1/todos');

        $this->assertResponseOk();
        $this->assertEquals('application/json', $this->response->headers->get('Content-Type'));
        $this->assertCount(APITest::DEFAULT_TODO_NUMBER, json_decode($this->response->getContent()));
    }

    public function testListTodoWithEmpty()
    {
        $this->call('GET', '/v1/todos');

        $this->assertResponseOk();
        $this->assertEquals('application/json', $this->response->headers->get('Content-Type'));
        $this->assertCount(0, json_decode($this->response->getContent()));
    }

    public function testViewNotFoundTodo()
    {
        $this->json('GET', '/v1/todos/1')
            ->seeJson([
                'message' => 'Couldn\'t find the todo'
            ]);
        $this->assertEquals(500, $this->response->getStatusCode());
    }

    public function testViewExistTodo()
    {
        factory(App\Models\Todo::class, 1)->create();

        $this->json('GET', "/v1/todos/1")
            ->seeJson([
                'id' => 1
            ]);
        $this->assertResponseOk();
    }

    public function testCreateTodoWithEmptyRequest()
    {
        $this->json('POST', '/v1/todos', [])
            ->seeJson([
                "title" => ["The title field is required."]
            ]);
        $this->assertEquals(422, $this->response->getStatusCode());
    }

    public function testCreateTodoWithMissingTitle()
    {
        $this->json('POST', '/v1/todos', [
                'todo_groups_id' => '1'
            ])
            ->seeJson([
                "title" => ["The title field is required."]
            ]);
        $this->assertEquals(422, $this->response->getStatusCode());
    }

    public function testCreateTodoSuccessfully()
    {
        factory(App\Models\Todo::class, APITest::DEFAULT_TODO_NUMBER)->create();
        $this->json('POST', '/v1/todos', [
                'title' => 'Test Todo 1',
                'todo_groups_id' => '1'
            ])
            ->seeJson([
                'title' => 'Test Todo 1'
            ]);
        $this->assertResponseOk();

        $this->call('GET', '/v1/todos');
        $this->assertResponseOk();
        $this->assertEquals('application/json', $this->response->headers->get('Content-Type'));
        $this->assertCount(APITest::DEFAULT_TODO_NUMBER + 1, json_decode($this->response->getContent()));
    }

    public function testUpdateNotFoundTodo()
    {
        $response = $this->call('PUT', '/v1/todos/1', [
            'title' => 'Test Todo 1'
        ]);
        $this->assertEquals(500, $this->response->getStatusCode());
        $this->assertEquals('application/json', $this->response->headers->get('Content-Type'));
    }

    public function testUpdateTodoSuccess()
    {
        factory(App\Models\Todo::class, APITest::DEFAULT_TODO_NUMBER)->create();
        $title = 'Test Todo 1 Updated';
        $this->json('PUT', '/v1/todos/1', [
                'title' => $title
            ])
            ->seeJson([
                'title' => $title
            ]);
        $this->assertResponseOk();
    }

    public function testDeleteNotFoundTodo()
    {
        $this->json('DELETE', '/v1/todos/1')
            ->seeJson([
                'message' => 'Couldn\'t find the todo'
            ]);
        $this->assertEquals(500, $this->response->getStatusCode());
    }

    public function testDeleteTodoSuccessfully()
    {
        factory(App\Models\Todo::class, APITest::DEFAULT_TODO_NUMBER)->create();
        $this->call('DELETE', '/v1/todos/1');
        $this->assertResponseOk();
        $this->assertEquals('application/json', $this->response->headers->get('Content-Type'));

        $this->call('GET', '/v1/todos');
        $this->assertEquals('application/json', $this->response->headers->get('Content-Type'));
        $this->assertCount(APITest::DEFAULT_TODO_NUMBER - 1, json_decode($this->response->getContent()));
    }

    public function testMoveNotFoundTodo()
    {
        $this->json('POST', '/v1/todos/1/move', ['prior_sibling_id' => 3])
            ->seeJson([
                'message' => 'Couldn\'t find the todo'
            ]);
        $this->assertEquals(500, $this->response->getStatusCode());
    }

    public function testMoveTodoSuccess()
    {
        $todos = factory(App\Models\Todo::class, APITest::DEFAULT_TODO_NUMBER)->make()->toArray();

        foreach ($todos as $todo) {
            $this->json('POST', '/v1/todos', $todo);
            $this->assertResponseOk();
        }

        $this->json('POST', '/v1/todos/3/move', ['prior_sibling_id' => ''])
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
