<?php

namespace Adminx\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
        //$this->withFactories(__DIR__ . '/factories');
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        //include_once __DIR__.'/../tests/database/migrations/create_users_table.php.stub';
        //(new \CreateUsersTable())->up();
    }
}
