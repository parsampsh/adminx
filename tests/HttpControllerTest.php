<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under GPL-v3.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Tests;

use Adminx\Tests\TestCase;

class HttpControllerTest extends TestCase
{
    public function test_user_should_be_login_to_acces_the_panel()
    {
        $admin = new \Adminx\Core;

        $admin->set_title('Sometitle');
        
        $admin->register('/admin');

        $this->get('/admin')->assertStatus(302);

        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertStatus(200);
    }

    public function test_access_middleware_should_return_true_to_user_access()
    {
        $admin = new \Adminx\Core;

        $admin->set_middleware(function () {
            return false;
        });
        
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertStatus(403);

        $admin = new \Adminx\Core;

        $admin->set_middleware(function ($user) {
            return $user->username === 'manager';
        });

        $user->username .= '1';
        $user->save();
        
        $admin->register('/admin');

        $this->actingAs($user)->get('/admin')->assertStatus(403);

        $user->username = 'manager';
        $user->save();

        $this->actingAs($user)->get('/admin')->assertStatus(200);
    }

    public function test_user_info_is_showed()
    {
        $admin = new \Adminx\Core;
        
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin');
        $res->assertStatus(200);
        $res->assertSee('<span class="mr-2 d-none d-lg-inline text-gray-600 small">unset</span>', false);
        $res->assertSee('<img class="img-profile rounded-circle" src="unset">', false);

        $admin = new \Adminx\Core;

        $admin->set_userinfo(function ($user) {
            return [
                'username' => $user->email,
                'image' => '/link',
            ];
        });
        
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin');
        $res->assertStatus(200);
        $res->assertSee('<span class="mr-2 d-none d-lg-inline text-gray-600 small">' . $user->email . '</span>', false);
        $res->assertSee('<img class="img-profile rounded-circle" src="/link">', false);
    }

    public function test_logout_button_link_is_valid()
    {
        $admin = new \Adminx\Core;
        
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin');
        $res->assertStatus(200);
        $res->assertSee('<a class="btn btn-primary" href="' . url('/auth/logout') . '">Logout</a>', false);

        $admin = new \Adminx\Core;

        $admin->set_logout('/link/to/logout');
        
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin');
        $res->assertStatus(200);
        $res->assertSee('<a class="btn btn-primary" href="' . url('/link/to/logout') . '">Logout</a>', false);
    }

    public function test_copyright_message_is_valid()
    {
        $admin = new \Adminx\Core;
        
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin');
        $res->assertStatus(200);
        $res->assertSee('<span>Copyright</span>', false);

        $admin = new \Adminx\Core;

        $admin->set_copyright('All rights reserved');
        
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin');
        $res->assertStatus(200);
        $res->assertSee('<span>All rights reserved</span>', false);
    }

    public function test_links_in_menu_is_showed()
    {
        (new \Adminx\Core)
            ->add_link('Test link', 'https://example.com', 'blank', 'fa fa-user')
            ->register('/admin')
            ;

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin');
        $res->assertStatus(200);
        $res->assertSee('<a class="nav-link" href="https://example.com" target="blank">', false);
        $res->assertSee('<i class="fa fa-user"></i><span>Test link</span></a>', false);
    }

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
}
