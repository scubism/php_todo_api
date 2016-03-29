<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTodoGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
	if (Schema::hasTable('todo_groups')) {
            Schema:create('todo_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title',255);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
	Schema::dropIfExists('todo_groups');
    }
}
