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

class PageSystemTest extends TestCase
{
    public function test_page_is_showed_in_menu()
    {
        $admin = new \Adminx\Core;
        $admin->add_page('Test page', 'my-page', function () {
        }, 'fa fa-user');
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin');
        $res->assertStatus(200);
        $res->assertSee('<a class="nav-link" href="' . $admin->url('page/my-page') . '" target="">', false);
        $res->assertSee('<i class="fa fa-user"></i><span>Test page</span></a>', false);
    }

    public function test_page_can_be_showed()
    {
        $admin = new \Adminx\Core;
        $admin->add_page('Test page', 'my-page', function () {
            return 'hello world. i am a page';
        }, 'fa fa-user');
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/page/my-page');
        $res->assertStatus(200);
        $res->assertSee('hello world. i am a page', false);
    }

    public function test_page_can_be_showed_with_request_object()
    {
        $admin = new \Adminx\Core;
        $admin->add_page('Test page', 'my-page', function ($request) {
            return 'hello world. i am a page. value is ' . $request->get('the-value');
        }, 'fa fa-user');
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/page/my-page?the-value=hello');
        $res->assertStatus(200);
        $res->assertSee('hello world. i am a page. value is hello', false);
    }

    public function test_index_page_working()
    {
        $admin = new \Adminx\Core;
        $admin->add_page('The index page for adminx', '.', function ($request) {
            return 'The index page of adminx';
        });
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin');
        $res->assertStatus(200);
        $res->assertSee('The index page for adminx', false);
        $res->assertSee('The index page of adminx', false);
    }
}
