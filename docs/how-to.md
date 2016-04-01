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

# Renaming Columns:
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

### Create a model

### Create a controller
