<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2021 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Tests;

use Adminx\Tests\TestCase;

class HttpControllerTest extends TestCase
{
    public function test_user_should_be_login_to_access_the_panel()
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

    public function test_localization_words_working(){
        // note: the following texts used as localization values are "Persian Finglish". ignore them :)
        $admin = new \Adminx\Core;
        $admin->set_word('logout.title', 'Aya amade logout kardan hastid?');
        $admin->set_word('logout.btn', 'Khorooj');
        $admin->set_word('logout.message', 'Baraye Khorooj Rooye dokme zir click konid.');
        $admin->set_word('logout.cancel', 'Laghv');
        $admin->set_word('menu.dashboard', 'Safhe asli');
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();
        $res = $this->actingAs($user)->get('/admin');
        $res->assertStatus(200);

        $res->assertSee('<h5 class="modal-title" id="exampleModalLabel">Aya amade logout kardan hastid?</h5>', false);
        $res->assertSee('<button class="btn btn-secondary" type="button" data-dismiss="modal">Laghv</button>', false);
        $res->assertSee('<div class="modal-body">Baraye Khorooj Rooye dokme zir click konid.</div>', false);
        $res->assertSee('<span>Safhe asli</span></a>', false);
        $res->assertSee('<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Khorooj</a>', false);
        $res->assertSee('<a class="btn btn-primary" href="' . url($admin->get_logout()) . '">Khorooj</a>', false);
    }
}
