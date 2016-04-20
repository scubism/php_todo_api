<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class APITest extends TestCase {
	public function testAllTodos() {
		$this->json('GET', '/v1/todos')
			->seeJson([
				'id' => '1'
			]);
	}

	public function testViewTodo() {
		$response = $this->call('GET', '/v1/todos/0');
		$this->assertFalse($response);

		$this->json('GET', '/v1/todos/1')
			->seeJson([
				'id' => '1'
			]);
	}

	public function testCreateTodo() {
		$this->json('POST', '/v1/todos', ['title' => 'Test Todo 1', 
			'duedate' => '2016-04-20 10:42', 
			'color' => 'black', 
			'todo_groups_id' => '1'])
			->seeJson([
				'title' => 'Test Todo 1',
				'duedate' => '2016-04-20 10:42', 
				'color' => 'black', 
				'todo_groups_id' => '1'
			]);

		$response = $this->call('POST', '/v1/todos', ['title' => 'Test Todo 1', 
			'duedate' => '2016-04-20 10:42', 
			'color' => 'black', 
			'todo_groups_id' => '1']);
		$this->assertEquals(200, $response->status());
		$this->assertEquals('application/json', $response->header('Content-Type'));
	}

	public function testUpdateTodo() {
		$response = $this->call('PUT', '/v1/todos/0', [
			'title' => 'Test Todo 0',
			'duedate' => '2016-04-20 10:42', 
			'color' => 'black', 
			'todo_groups_id' => '1'
		]);
		$this->assertEquals(422, $response->status());

		$this->json('PUT', '/v1/todos/1', ['title' => 'Test Todo 1 Updated', 
			'duedate' => '2016-04-20 10:42', 
			'color' => 'black', 
			'todo_groups_id' => '1'])
			->seeJson([
				'title' => 'Test Todo 1 Updated',
				'duedate' => '2016-04-20 10:42', 
				'color' => 'black', 
				'todo_groups_id' => '1'
			]);
	}

	public function testDeleteTodo() {
		$response = $this->call('PUT', '/v1/todos/0');
		$this->assertEquals(422, $response->status());

		$this->json('PUT', '/v1/todos/1')
			->seeJson([
				'title' => 'Test Todo 1 Updated',
				'duedate' => '2016-04-20 10:42', 
				'color' => 'black', 
				'todo_groups_id' => '1'
			]);
	}

	public function testMoveTodo() {
		$response = $this->call('PUT', '/v1/todos/0/move', ['prior_sibling_id' => 2]);
		$this->assertEquals(422, $response->status());

		$this->json('PUT', '/v1/todos/1/move', ['prior_sibling_id' => 2])
			->seeJson([
				'title' => 'Test Todo 1 Updated',
				'duedate' => '2016-04-20 10:42', 
				'color' => 'black', 
				'todo_groups_id' => '1'
			]);
	}
}