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
        include_once __DIR__ . '/../src/Adminx/Migrations/create_adminx_tables_1.php';
        (new \CreateUsersTable())->up();
        (new \Adminx\Migrations\CreateAdminxTables())->up();
    }
}
