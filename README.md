# Adminx Library

[![Latest Stable Version](https://img.shields.io/packagist/v/parsampsh/adminx.svg)](https://packagist.org/packages/parsampsh/adminx)

Adminx is a library to create and handle admin panel automaticaly in laravel applications.

## Why Adminx?
Some of Adminx Features:

- Easy to install and configure
- Secure
- Beautiful default frontend
- Customizing Admin panel general information
- Adding custom pages and links to the admin panel Menu
- Automatic and advanced CRUD for models with useful options
- Fully matched with your database models
- Handling 1 to n and n to n relations in database
- Custom actions for model datatable
- Handling admins Activities and logs
- Customizable frontend layout
- Customizable language and localization
- RTL layout
- Several builtin themes
- Advanced permission and group handling
- Customizing Authorization
- Advanced options for model datatable
- Virtual fields for models
- Search system
- Advanced options for filtering data in datatable
- Customizable Create/Update forms
- Able to Use for End-User
- Matched with laravel authentication system
- Plugin system

## Preview

<img src="/doc/images/preview.png" />

## Authors
This library is written by [parsampsh](https://github.com/parsampsh).

## Get started
To get started with this package, do the following steps in your laravel project:

- Add the package via composer: `$ composer require parsampsh/adminx`
- Publish public assets: `$ php artisan vendor:publish --provider="Adminx\AdminxServiceProvider"`
- Run the migrations: `$ php artisan migrate`

Then, adminx is ready to be used. create `routes/adminx.php` file and go to `app/Providers/RouteServiceProvider.php` and inclue that in **End of the `boot` method**:

```php
// ...

include base_path('routes/adminx.php');

// ...
```

Then, write this code in `routes/adminx.php`:

```php
// ...

$admin = new \Adminx\Core;

// set the admin panel configurations on $admin object

// register the admin panel
$admin->register('/admin'); // `/admin` is the route of admin panel

// ...
```

Now, run `$ php artisan optimize`, `$ php artisan serve` and goto `/admin` page. Remember that to access to the admin panel you must be logged in using the laravel auth.

Enjoy it!

## Documentation
To learn how to use **Adminx**, read the documentation in [doc folder](/doc).

## Contribution Guide
If you want to Contribute to this project, Read [This Contribution Guide](/CONTRIBUTING.md)

## Changelog
See the Changelog of Adminx [here](/CHANGELOG.md).

## Security Policy
See the Adminx security policy [here](/SECURITY.md).

## License
Adminx is licensed under [MIT](/LICENSE).
