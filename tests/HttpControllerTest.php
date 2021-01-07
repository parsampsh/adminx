<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2021 parsa shahmaleki <parsampsh@gmail.com>
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

    public function test_model_page_middleware_works(){
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'user'
        ]);
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/model/some-model');
        $res->assertStatus(404);

        $res = $this->actingAs($user)->get('/admin/model/user');
        $res->assertStatus(200);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'user',
            'middleware' => (function($user){
                return false;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/user');
        $res->assertStatus(403);
    }

    public function test_fields_titles_option_for_models_works(){
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'fields_titles' => [
                'email' => 'The Email',
            ]
        ]);
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);

        $res->assertSee('<th>The Email</th>', false);
    }

    public function test_no_table_footer_option_works(){
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
        ]);
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);

        $res->assertSee('<tfoot><tr>', false);
        $res->assertSee('</tr></tfoot>', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'no_table_footer' => true,
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);

        $res->assertDontSee('<tfoot><tr>', false);
        $res->assertDontSee('</tr></tfoot>', false);
    }

    public function test_fields_values_option_works(){
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
        ]);
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);

        $res->assertSee('<td>' . $user->email . '</td>', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'fields_values' => [
                'email' => (function($user){
                    return '<a>' . $user->email . '</a>';
                }),
            ],
        ]);
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);

        $res->assertDontSee('<td>' . $user->email . '</td>', false);
        $res->assertSee('<td><a>' . $user->email . '</a></td>', false);
    }

    public function test_filter_data_option_works(){
        $user = \App\Models\User::factory()->create();
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'filter_data' => (function($q) use ($user){
                return $q->where('email', $user->email);
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertSee('<td>' . $user->email . '</td>', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'filter_data' => (function($q) use ($user){
                return $q->where('email', '!=', $user->email);
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertDontSee('<td>' . $user->email . '</td>', false);
    }

    public function test_virtual_fields_option_works()
    {
        $user = \App\Models\User::factory()->create();
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'virtual_fields' => [
                'Something' => (function($row){
                    return 'hello ' . $row->email;
                }),
            ],
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertSee('<th>Something</th>', false);
        $res->assertSee('<td>hello ' . $user->email . '</td>', false);
    }

    public function test_custom_html_works(){
        $user = \App\Models\User::factory()->create();
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'custom_html' => (function(){
                return 'The top custom html';
            }),
            'custom_html_bottom' => (function(){
                return 'The bottom custom html';
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertSee('The top custom html', false);
        $res->assertSee('The bottom custom html', false);
    }

    public function test_search_system_works(){
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertDontSee('<input value="" name="search" type="text" class="form-control bg-light border-0 small" placeholder="Search here..." aria-label="Search" aria-describedby="basic-addon2">', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'search' => (function($q, $w){
                return $q;
            })
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertSee('<input value="" name="search" type="text" class="form-control bg-light border-0 small" placeholder="Search here..." aria-label="Search" aria-describedby="basic-addon2">', false);

        $res = $this->actingAs($user)->get('/admin/model/the-users?search=hello');
        $res->assertStatus(200);
        $res->assertSee('<input value="hello" name="search" type="text" class="form-control bg-light border-0 small" placeholder="Search here..." aria-label="Search" aria-describedby="basic-addon2">', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'search' => (function($q, $w){
                return $q;
            }),
            'search_hint' => 'search for something',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertSee('<input value="" name="search" type="text" class="form-control bg-light border-0 small" placeholder="search for something" aria-label="Search" aria-describedby="basic-addon2">', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'search' => (function($q, $w){
                // reverse search
                return $q->where('email', '<>', $w);
            }),
            'search_hint' => 'search for something',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertSee('<td>' . $user->email . '</td>', false);

        $res = $this->actingAs($user)->get('/admin/model/the-users?search=' . $user->email);
        $res->assertStatus(200);
        $res->assertDontSee('<td>' . $user->email . '</td>', false);
    }

    public function test_delete_button_will_be_showed(){
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertDontSee('<input type="hidden" name="delete" value="' . $user->id . '" />', false);

        \Adminx\Access::add_permission_for_user($user, 'the-users.delete');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertSee('<input type="hidden" name="delete" value="' . $user->id . '" />', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'delete_middleware' => (function(){
                return false;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertDontSee('<input type="hidden" name="delete" value="' . $user->id . '" />', false);
    }

    public function test_user_need_permission_to_delete_row_and_row_can_be_deleted(){
        $user = \App\Models\User::factory()->create();

        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->delete('/admin/model/the-users', ['delete' => $user1->id]);
        $res->assertStatus(403);

        \Adminx\Access::add_permission_for_user($user, 'the-users.delete');

        $res = $this->actingAs($user)->delete('/admin/model/the-users', ['delete' => $user1->id]);
        $res->assertStatus(302);
        $this->assertEmpty(\App\Models\User::find($user1->id));

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'delete_middleware' => (function(){
                return false;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->delete('/admin/model/the-users', ['delete' => $user2->id]);
        $res->assertStatus(403);
    }
}
