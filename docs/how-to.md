## PHP TODO API - How to
We're using Lumen - A micro-framework based on Laravel core. So if you're familiar Laravel, you might not need to read these instructions.

### Database migration

__Generating migrations:__
```
php artisan make:migration <migration name>
# Example:
php artisan make:migration add_todo_table
```

__Running migrations:__
```
php artisan migrate
```

__Creating Table__
```
Schema::create('users', function (Blueprint $table) {
    $table->increments('id');
});
# column method: https://laravel.com/docs/5.2/migrations#creating-columns

# Checking table/column existence
if (Schema::hasTable('users')) {
    //
}

if (Schema::hasColumn('users', 'email')) {
    //
}
```

__Renaming / Dropping Table__
```
# Rename table
Schema::rename($from, $to);
# Drop table
Schema::dropIfExists('users');
```

__Adding / Modifying / Dropping Columns__
```
# Creating Columns:
Schema::table('users', function ($table) {
    $table->string('email');
});

# Modifying Columns:
Schema::table('users', function ($table) {
    $table->string('name', 50)->change();
});

# Renaming Columns: (require `doctrine/dbal` to be able to rename column)
Schema::table('users', function ($table) {
    $table->renameColumn('from', 'to');
});

# Drop Columns:
Schema::table('users', function ($table) {
    $table->dropColumn(['votes', 'avatar', 'location']);
});
```

__Rollback__
```
# Undo the last migration
php artisan migrate:rollback
# Rollback all migrations
php artisan migrate:reset
```

You can read more detail in [Laravel Migration document page](https://laravel.com/docs/5.2/migrations)

### Create a route
File `app/Http/routes.php`:

__Available Router Methods__
```
$app->get($uri, $callback);
$app->post($uri, $callback);
$app->put($uri, $callback);
$app->patch($uri, $callback);
$app->delete($uri, $callback);
$app->options($uri, $callback);

# Basic Example
$app->get('foo', function () {
    return 'Hello World';
});
```

__Route Parameters__
```
$app->get('user/{id}', function ($id) {
    return 'User '.$id;
});
```

__Named Routes__
```
$app->get('profile', [
    'as' => 'profile', 'uses' => 'UserController@showProfile'
]);
```

Please read more in [Lumen Routing page](https://lumen.laravel.com/docs/5.2/routing)

### Create a model
- Create a files in `app/DataAccess/Eloquent`
```
mkdir -p app/DataAccess/Eloquent
touch app/DataAccess/Eloquent/User.php
```

- Sample model:
```
# app/DataAccess/Eloquent.php
<?php

namespace App\DataAccess\Eloquents;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
}
```

Read more about [Eloquent ORM](https://laravel.com/docs/5.2/eloquent)

### Create a controller

- Create a files in `app/Http/Controllers`
```
touch app/Http/Controllers/UsersController.php
```

- Sample controller:
```
# app/Http/Controllers/UsersController.php
<?php

namespace App\Http\Controllers;

use App\DataAccess\Eloquent; # Use User model

class UsersController extends Controller
{
    /**
     * Retrieve the user for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return User::findOrFail($id);
    }
}

```

We can route to the controller action like so:
```
$app->get('user/{id}', 'UserController@show');
```

Read more about [Lumen Controller](https://lumen.laravel.com/docs/5.2/controllers)

All the information above could be found at [Lumen Documentation](https://lumen.laravel.com/docs)
