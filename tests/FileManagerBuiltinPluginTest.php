<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2022 Parsa Shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Tests;

use Adminx\Tests\TestCase;

class FileManagerBuiltinPluginTest extends TestCase
{
    public function test_slug_option_works()
    {
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin);
        $admin->register('/admin');

        $this->actingAs($user)->get('/admin/page/file-manager')->assertOk();

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'page_slug' => 'my-fm',
        ]);
        $admin->register('/admin');

        $this->actingAs($user)->get('/admin/page/file-manager')->assertStatus(404);
        $this->actingAs($user)->get('/admin/page/my-fm')->assertOk();
    }

    public function test_access_middleware_works()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'testuserforfilemanager@example.com',
        ]);
        $user2 = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin);
        $admin->register('/admin');

        $this->actingAs($user)->get('/admin/page/file-manager')->assertOk();
        $this->actingAs($user2)->get('/admin/page/file-manager')->assertOk();

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'access_middleware' => function () {
                return false;
            },
        ]);
        $admin->register('/admin');

        $this->actingAs($user)->get('/admin/page/file-manager')->assertStatus(403);
        $this->actingAs($user2)->get('/admin/page/file-manager')->assertStatus(403);

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'access_middleware' => function ($user) {
                return $user->email === 'testuserforfilemanager@example.com';
            },
        ]);
        $admin->register('/admin');

        $this->actingAs($user)->get('/admin/page/file-manager')->assertOk();
        $this->actingAs($user2)->get('/admin/page/file-manager')->assertStatus(403);
    }

    public function test_files_can_be_shown()
    {
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'dirs' => [
                realpath(__DIR__ . '/../tests'),
                __DIR__ . '/../src',
            ]
        ]);
        $admin->register('/admin');

        $response = $this->actingAs($user)->get('/admin/page/file-manager');
        $response->assertOk();
        $response->assertSee('tests');
        $response->assertSee('src');
        $response->assertSee(realpath(__DIR__ . '/../tests'));
        $response->assertSee(realpath(__DIR__ . '/../src'));

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=/notfound1234');
        $response->assertSee('not found');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . __DIR__ . '/../tests/testfile.txt');
        $response->assertSee('hello world from the test file');
        $response->assertSee(realpath(__DIR__ . '/../tests/testfile.txt'));
        $response->assertSee(dirname(__DIR__ . '/../tests/testfile.txt'));

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . __DIR__ . '/../tests/');
        $response->assertSee('FileManagerBuiltinPluginTest.php');
        $response->assertSee(realpath(__DIR__ . '/../tests/'));
        $response->assertSee(dirname(__DIR__ . '/../tests/'));
    }
}
