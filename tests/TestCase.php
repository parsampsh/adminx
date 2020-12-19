<?php

namespace Adminx\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

require_once __DIR__ . '/database/User.php';
require_once __DIR__ . '/database/factories/UserFactory.php';

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        \Illuminate\Support\Facades\Route::get('/login', function () {
            return 'the login route';
        })->name('login');
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        include_once __DIR__ . '/database/migrations/create_users_table.php.1';
        include_once __DIR__ . '/../src/Adminx/Migrations/2020_12_18_180436_create_adminx_tables.php';
        (new \CreateUsersTable())->up();
        (new \CreateAdminxTables())->up();
    }
}
