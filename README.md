# Adminx Library
Adminx is a library to create and handle admin panel automaticaly in laravel applications.

### Note: This library is in development state and is not ready

## Why Adminx?
Some of Adminx Features:

- Easy to Install and Configure
- Secure
- Beautiful default frontend
- Customizing Admin panel general information
- Adding custom pages and links to admin panel Menu
- Automatic and Advance CRUD for models with useful options
- Fully match with your database models
- Handling 1 to n and n to n relations in database
- Custom actions for model datatable
- Handling Admins Actibvity Logs (comming soon...)
- Able to Customize frontend layout
- Able to Customizing by language and localization
- RTL layout
- Advance Permission and Group handling
- Customizing Authorization
- Advance options for model Datatable
- Virtual fields for models
- Search
- Advance options for filtering data in datatable
- Customizing Create/Update forms
- Able to Use for End-User
- Match with laravel Auth
- Plugin system
- Builtin Data Backup plugin
- Cache Handling to Optimize database usage (comming soon...)

## Authors
this package is written by [parsampsh](https://github.com/parsampsh).

## Get started
to get started with this package, do the following steps in your laravel project:

- Add the package via composer: `$ composer require parsampsh/adminx`
- Publish public assets: `$ php artisan vendor:publish --provider="Adminx\AdminxServiceProvider"`
- Run the migrations: `$ php artisan migrate`

then, adminx is ready to use. create `routes/adminx.php` file and go to `app/Providers/RouteServiceProvider.php` and inclue that in **End of `boot` method**:

```php
// ...

include base_path('routes/adminx.php');

// ...
```

then, write this code in `routes/adminx.php`:

```php
// ...

$admin = new \Adminx\Core;

// set the admin panel configurations on $admin object

// register the admin panel
$admin->register('/admin'); // `/admin` is the route of admin panel

// ...
```

now, run `$ php artisan optimize`, `$ php artisan serve` and goto `/admin` page. Remember that to access to the admin panel you should be login using laravel auth.

Enjoy it!

## Documentation
To learn how to use **Adminx**, read the documentation in [doc folder](/doc).
