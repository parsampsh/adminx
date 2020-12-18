# Adminx library
Adminx is a library to create and handle admin panel automaticaly in laravel applications.

### Note: This library is in development state and is not ready

### Authors
this package is written by [parsampsh](https://github.com/parsampsh).

### Get started
to get started with this package, do the following steps in your laravel project:

- Add the package via composer: `$ composer require parsampsh/adminx`
- Run the migrations: `$ php artisan migrate`

then, adminx is ready to use. create a file in `routes/adminx.php` in your project. then go to `app/Providers/RouteServiceProvider.php` and add this code to **End of `boot` method**:

```php
// ...
// include the file
include_once base_path('routes/adminx.php');
// ...
```

then, write this content to `routes/adminx.php` file:

```php
<?php

$admin = new \Adminx\Core;

// set the admin panel configurations on $admin object

// register the admin panel
$admin->register('/admin'); // `/admin` is the route of admin panel
```

now, run `$ php artisan optimize`, serve your application and goto `/admin` page.

Enjoy it!

## Documentation
To learn how to use **Adminx**, read the documentation in [doc folder](/doc).
