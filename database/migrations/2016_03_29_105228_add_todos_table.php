<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
	    if (!Schema::hasTable('todos')) {
            Schema:create('todos', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title',255);
                $table->dateTime('duedate')->nullable();
                $table->string('color', 255);
                $table->integer('group_id');
            });
        }
	    Schema::table('todos', function($table) {
            $table->dateTime('duedate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
	    Schema::dropIfExists('todos');
    }
}
