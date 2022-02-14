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

    public function test_path_must_be_valid()
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

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . __DIR__ . '/..');
        $response->assertSee('not found');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . realpath(__DIR__ . '/../tests/'));
        $response->assertDontSee(dirname(__DIR__ . '/../tests/'));
    }

    public function test_can_see_middleware_works()
    {
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'dirs' => [
                realpath(__DIR__ . '/../tests'),
            ],
            'can_see' => (function ($u, $file) {
                return !($u->id === $user->id && $file->path === realpath(__DIR__ . '/../tests/FileManagerBuiltinPluginTest.php'));
            }),
        ]);
        $admin->register('/admin');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . realpath(__DIR__ . '/../tests/'));
        $response->assertDontSee(realpath(__DIR__ . '/../tests/FileManagerBuiltinPluginTest.php'));

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'dirs' => [
                realpath(__DIR__ . '/../tests'),
            ],
        ]);
        $admin->register('/admin');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . realpath(__DIR__ . '/../tests/'));
        $response->assertSee(realpath(__DIR__ . '/../tests/FileManagerBuiltinPluginTest.php'));
    }

    public function test_can_read_mddleware_works()
    {
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'dirs' => [
                realpath(__DIR__ . '/../tests'),
            ],
            'can_read' => (function ($u, $file) use ($user) {
                return !($u->id === $user->id && $file->path === realpath(__DIR__ . '/../tests/FileManagerBuiltinPluginTest.php'));
            }),
        ]);
        $admin->register('/admin');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . realpath(__DIR__ . '/../tests/'));
        $response->assertSee('fa fa-lock');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . realpath(__DIR__ . '/../tests/FileManagerBuiltinPluginTest.php'));
        $response->assertStatus(404);

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'dirs' => [
                realpath(__DIR__ . '/../tests'),
            ],
        ]);
        $admin->register('/admin');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . realpath(__DIR__ . '/../tests/'));
        $response->assertDontSee('fa fa-lock');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . realpath(__DIR__ . '/../tests/FileManagerBuiltinPluginTest.php'));
        $response->assertOk();
        $response->assertSee(realpath(__DIR__ . '/../tests/FileManagerBuiltinPluginTest.php'));
    }

    public function test_can_delete_mddleware_works()
    {
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'dirs' => [
                realpath(__DIR__ . '/../tests/test-dir'),
            ],
            'can_delete' => (function ($u, $file) {
                return ($file->path !== realpath(__DIR__ . '/../tests/test-dir/undeletable.txt') && $file->path !== realpath(__DIR__ . '/../tests/test-dir/dir-undeletable') && $file->path !== realpath(__DIR__ . '/../tests/test-dir/dir-undeletable/sub/a.txt'));
            }),
        ]);
        $admin->register('/admin');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . realpath(__DIR__ . '/../tests/test-dir/'));
        $response->assertSee('fa fa-trash');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?currentLoc=' . realpath(__DIR__ . '/../tests/test-dir/dir-undeletable/sub/'));
        $response->assertDontSee('fa fa-trash');

        $response = $this->actingAs($user)->post('/admin/page/file-manager', ['delete_file' => realpath(__DIR__ . '/../tests/test-dir/dir-undeletable/sub/a.txt')]);
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post('/admin/page/file-manager', ['delete_file' => realpath(__DIR__ . '/../tests/test-dir/dir-undeletable/')]);
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post('/admin/page/file-manager', ['delete_file' => realpath(__DIR__ . '/../tests/')]);
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post('/admin/page/file-manager', ['delete_file' => realpath(__DIR__ . '/../tests/test-dir/first.txt')]);
        $this->assertFalse(file_exists(realpath(__DIR__ . '/../tests/test-dir/first.txt')));
        touch(__DIR__ . '/../tests/test-dir/first.txt');

        $response = $this->actingAs($user)->post('/admin/page/file-manager', ['delete_file' => realpath(__DIR__ . '/../tests/test-dir/subdir')]);
        $this->assertFalse(file_exists(realpath(__DIR__ . '/../tests/test-dir/subdir')));

        mkdir(__DIR__ . '/../tests/test-dir/subdir');
        mkdir(__DIR__ . '/../tests/test-dir/subdir/a');
        touch(__DIR__ . '/../tests/test-dir/subdir/a/test4.txt');
        touch(__DIR__ . '/../tests/test-dir/subdir/test2.txt');
        touch(__DIR__ . '/../tests/test-dir/subdir/test3.txt');
    }

    public function test_copy_and_cut_work()
    {
        $user = \App\Models\User::factory()->create();
        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'dirs' => [
                realpath(__DIR__ . '/../tests/test-dir'),
            ],
            'can_delete' => (function ($u, $file) {
                return ($file->path !== realpath(__DIR__ . '/../tests/test-dir/undeletable.txt') && $file->path !== realpath(__DIR__ . '/../tests/test-dir/dir-undeletable') && $file->path !== realpath(__DIR__ . '/../tests/test-dir/dir-undeletable/sub/a.txt'));
            }),
            'can_read' => (function ($u, $file) {
                return ($file->path !== realpath(__DIR__ . '/../tests/test-dir/un-readable.txt'));
            }),
            'can_write' => (function ($u, $file) {
                return ($file->path !== realpath(__DIR__ . '/../tests/test-dir/un-writable'));
            }),
        ]);
        $admin->register('/admin');

        $response = $this->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/') , [
            'copy_file' => realpath(__DIR__ . '/../tests/test-dir/un-readable.txt'),
        ]);
        $response->assertStatus(403);

        $response = $this->withSession([
            'adminx_filemanager_clipboard' => realpath(__DIR__ . '/../tests/test-dir/un-readable.txt'),
        ])->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/') , [
            'paste_file' => '1',
        ]);
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/') , [
            'cut_file' => realpath(__DIR__ . '/../tests/test-dir/un-readable.txt'),
        ]);
        $response->assertStatus(403);

        $response = $this->withSession([
            'adminx_filemanager_clipboard' => realpath(__DIR__ . '/../tests/test-dir/un-readable.txt'),
            'adminx_filemanager_clipboard_is_cut' => true,
        ])->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/') , [
            'paste_file' => '1',
        ]);
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/') , [
            'cut_file' => realpath(__DIR__ . '/../tests/test-dir/undeletable.txt'),
        ]);
        $response->assertStatus(403);

        $response = $this->withSession([
            'adminx_filemanager_clipboard' => realpath(__DIR__ . '/../tests/test-dir/undeletable.txt'),
            'adminx_filemanager_clipboard_is_cut' => true,
        ])->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/') , [
            'paste_file' => '1',
        ]);
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/') , [
            'copy_file' => realpath(__DIR__ . '/../tests/test-dir/first.txt'),
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('adminx_filemanager_clipboard', realpath(__DIR__ . '/../tests/test-dir/first.txt'));
        $response->assertSessionHas('adminx_filemanager_clipboard_is_cut', false);

        $response = $this->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/') , [
            'cut_file' => realpath(__DIR__ . '/../tests/test-dir/first.txt'),
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('adminx_filemanager_clipboard', realpath(__DIR__ . '/../tests/test-dir/first.txt'));
        $response->assertSessionHas('adminx_filemanager_clipboard_is_cut', true);

        $response = $this->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/') , [
            'cut_file' => realpath(__DIR__ . '/../tests/test-dir/'),
        ]);
        $response->assertStatus(403);

        $response = $this->withSession([
            'adminx_filemanager_clipboard' => realpath(__DIR__ . '/../tests/test-dir/'),
            'adminx_filemanager_clipboard_is_cut' => true,
        ])->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/') , [
            'paste_file' => '1',
        ]);
        $response->assertStatus(403);

        $this->assertFalse(file_exists(realpath(__DIR__ . '/../tests/test-dir/subdir/first.txt')));
        $this->assertFalse(file_exists(realpath(__DIR__ . '/../tests/test-dir/subdir/first.txt1')));
        $response = $this->withSession([
            'adminx_filemanager_clipboard' => realpath(__DIR__ . '/../tests/test-dir/first.txt'),
            'adminx_filemanager_clipboard_is_cut' => false,
        ])->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/subdir') , [
            'paste_file' => '1',
        ]);
        $response->assertStatus(302);
        $this->assertTrue(file_exists(realpath(__DIR__ . '/../tests/test-dir/subdir/first.txt')));

        $response = $this->withSession([
            'adminx_filemanager_clipboard' => realpath(__DIR__ . '/../tests/test-dir/first.txt'),
            'adminx_filemanager_clipboard_is_cut' => false,
        ])->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/subdir') , [
            'paste_file' => '1',
        ]);
        $response->assertStatus(302);
        $this->assertTrue(file_exists(realpath(__DIR__ . '/../tests/test-dir/subdir/first.txt1')));
        unlink(realpath(__DIR__ . '/../tests/test-dir/subdir/first.txt'));
        unlink(realpath(__DIR__ . '/../tests/test-dir/subdir/first.txt1'));

        $this->assertFalse(file_exists(realpath(__DIR__ . '/../tests/test-dir/un-writable/first.txt')));
        $response = $this->withSession([
            'adminx_filemanager_clipboard' => realpath(__DIR__ . '/../tests/test-dir/first.txt'),
            'adminx_filemanager_clipboard_is_cut' => false,
        ])->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/un-writable') , [
            'paste_file' => '1',
        ]);
        $response->assertStatus(403);
        $this->assertFalse(file_exists(realpath(__DIR__ . '/../tests/test-dir/un-writable/first.txt')));

        $response = $this->withSession([
            'adminx_filemanager_clipboard' => realpath(__DIR__ . '/../tests/test-dir/first.txt'),
            'adminx_filemanager_clipboard_is_cut' => false,
        ])->actingAs($user)->post('/admin/page/file-manager?currentLoc=/a', [
            'paste_file' => '1',
        ]);
        $response->assertStatus(403);

        $this->assertFalse(file_exists(realpath(__DIR__ . '/../tests/test-dir/subdir/first.txt')));
        $response = $this->withSession([
            'adminx_filemanager_clipboard' => realpath(__DIR__ . '/../tests/test-dir/first.txt'),
            'adminx_filemanager_clipboard_is_cut' => true,
        ])->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/subdir') , [
            'paste_file' => '1',
        ]);
        $response->assertStatus(302);
        $this->assertTrue(file_exists(realpath(__DIR__ . '/../tests/test-dir/subdir/first.txt')));
        $this->assertFalse(file_exists(realpath(__DIR__ . '/../tests/test-dir/first.txt')));
        unlink(realpath(__DIR__ . '/../tests/test-dir/subdir/first.txt'));
        touch(__DIR__ . '/../tests/test-dir/first.txt');

        $response = $this->withSession([
            'adminx_filemanager_clipboard' => realpath(__DIR__ . '/../tests/test-dir/first.txt'),
            'adminx_filemanager_clipboard_is_cut' => true,
        ])->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/un-writable') , [
            'paste_file' => '1',
        ]);
        $response->assertStatus(403);

        $response = $this->withSession([
            'adminx_filemanager_clipboard' => realpath(__DIR__ . '/../tests/test-dir/undeletable.txt'),
            'adminx_filemanager_clipboard_is_cut' => true,
        ])->actingAs($user)->post('/admin/page/file-manager?currentLoc='.realpath(__DIR__ . '/../tests/test-dir/') , [
            'paste_file' => '1',
        ]);
        $response->assertStatus(403);
    }

    public function test_file_can_be_downloaded()
    {
        $user = \App\Models\User::factory()->create();
        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'dirs' => [
                realpath(__DIR__ . '/../tests/test-dir'),
            ],
            'can_read' => (function ($u, $file) {
                return ($file->path !== realpath(__DIR__ . '/../tests/test-dir/un-readable.txt'));
            }),
        ]);
        $admin->register('/admin');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?download='.realpath(__DIR__ . '/../tests/test-dir/un-readable.txt'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get('/admin/page/file-manager?download='.(__DIR__ . '/../tests/test-dir/not-found.txt'));
        $response->assertStatus(404);

        $response = $this->actingAs($user)->get('/admin/page/file-manager?download='.realpath(__DIR__ . '/../tests/test-dir/first.txt'));
        $response->assertStatus(200);
        $this->assertNotEmpty($response->getFile());
    }

    public function test_directory_can_be_downloaded()
    {
        $user = \App\Models\User::factory()->create();
        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'dirs' => [
                realpath(__DIR__ . '/../tests/test-dir'),
            ],
            'can_download_directory' => (function ($u, $file) {
                return false;
            }),
        ]);
        $admin->register('/admin');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?download='.realpath(__DIR__ . '/../tests/test-dir'));
        $response->assertStatus(403);

        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'dirs' => [
                realpath(__DIR__ . '/../tests/test-dir'),
            ],
        ]);
        $admin->register('/admin');

        $response = $this->actingAs($user)->get('/admin/page/file-manager?download='.realpath(__DIR__ . '/../tests/test-dir'));
        $response->assertStatus(200);
    }

    public function test_rename_works()
    {
        $user = \App\Models\User::factory()->create();
        $admin = new \Adminx\Core;
        $admin->addPlugin(new \Adminx\Plugins\Builtins\FileManager\FileManagerPlugin, [
            'dirs' => [
                realpath(__DIR__ . '/../tests/test-dir'),
            ],
            'can_delete' => (function ($u, $file) {
                return ($file->path !== realpath(__DIR__ . '/../tests/test-dir/undeletable.txt') && $file->path !== realpath(__DIR__ . '/../tests/test-dir/dir-undeletable') && $file->path !== realpath(__DIR__ . '/../tests/test-dir/dir-undeletable/sub/a.txt'));
            }),
            'can_read' => (function ($u, $file) {
                return ($file->path !== realpath(__DIR__ . '/../tests/test-dir/un-readable.txt'));
            }),
        ]);
        $admin->register('/admin');

        $response = $this->actingAs($user)->post('/admin/page/file-manager', [
            'rename_file' => realpath(__DIR__ . '/../tests/test-dir/undeletable.txt'),
            'rename_to' => 'new-file.txt',
        ]);
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post('/admin/page/file-manager', [
            'rename_file' => realpath(__DIR__ . '/../tests/test-dir/un-readable.txt'),
            'rename_to' => 'new-file.txt',
        ]);
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post('/admin/page/file-manager', [
            'rename_file' => __DIR__ . '/../tests/test-dir/not-found-file.txt',
            'rename_to' => 'new-file.txt',
        ]);
        $response->assertStatus(404);

        $response = $this->actingAs($user)->post('/admin/page/file-manager', [
            'rename_file' => __DIR__ . '/../tests/test-dir/first.txt',
            'rename_to' => 'last.txt',
        ]);
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post('/admin/page/file-manager', [
            'rename_file' => __DIR__ . '/../tests/test-dir/first.txt',
            'rename_to' => 'new-file.txt',
        ]);
        $response->assertStatus(302);
        $this->assertFalse(file_exists(__DIR__ . '/../tests/test-dir/first.txt'));
        $this->assertTrue(file_exists(__DIR__ . '/../tests/test-dir/new-file.txt'));
        unlink(__DIR__ . '/../tests/test-dir/new-file.txt');
        touch(__DIR__ . '/../tests/test-dir/first.txt');

        $response = $this->actingAs($user)->post('/admin/page/file-manager', [
            'rename_file' => __DIR__ . '/../tests/test-dir/first.txt',
            'rename_to' => '../..\\new-file.txt',
        ]);
        $response->assertStatus(302);
        $this->assertFalse(file_exists(__DIR__ . '/../tests/test-dir/first.txt'));
        $this->assertTrue(file_exists(__DIR__ . '/../tests/test-dir/....new-file.txt'));
        unlink(__DIR__ . '/../tests/test-dir/....new-file.txt');
        touch(__DIR__ . '/../tests/test-dir/first.txt');
    }
}
