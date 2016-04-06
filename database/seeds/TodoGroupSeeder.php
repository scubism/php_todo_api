<?php

use Illuminate\Database\Seeder;
use App\Models\TodoGroup;

class TodoGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!TodoGroup::find(1)) {
            $group = new TodoGroup;
            $group->title = 'Group A';
            $group->save();
        }
    }
}
