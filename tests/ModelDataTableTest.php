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

class ModelDataTableTest extends TestCase
{
    public function test_model_page_middleware_works(){
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User'
        ]);
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/model/some-model');
        $res->assertStatus(404);

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'middleware' => (function($user){
                return false;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(403);
    }

    public function test_fields_titles_option_for_models_works(){
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'fields_titles' => [
                'email' => 'The Email',
            ]
        ]);
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);

        $res->assertSee('<th>The Email</th>', false);
    }

    public function test_no_table_footer_option_works(){
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
        ]);
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);

        $res->assertSee('<tfoot><tr>', false);
        $res->assertSee('</tr></tfoot>', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'no_table_footer' => true,
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);

        $res->assertDontSee('<tfoot><tr>', false);
        $res->assertDontSee('</tr></tfoot>', false);
    }

    public function test_fields_values_option_works(){
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
        ]);
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);

        $res->assertSee('<td>' . $user->email . '</td>', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'fields_values' => [
                'email' => (function($user){
                    return '<a>' . $user->email . '</a>';
                }),
            ],
        ]);
        $admin->register('/admin');

        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);

        $res->assertDontSee('<td>' . $user->email . '</td>', false);
        $res->assertSee('<td><a>' . $user->email . '</a></td>', false);
    }

    public function test_filter_data_option_works(){
        $user = \App\Models\User::factory()->create();
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'filter_data' => (function($q) use ($user){
                return $q->where('email', $user->email);
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User');
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

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);
        $res->assertDontSee('<td>' . $user->email . '</td>', false);
    }

    public function test_virtual_fields_option_works()
    {
        $user = \App\Models\User::factory()->create();
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'virtual_fields' => [
                'Something' => (function($row){
                    return 'hello ' . $row->email;
                }),
            ],
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);
        $res->assertSee('<th>Something</th>', false);
        $res->assertSee('<td>hello ' . $user->email . '</td>', false);
    }

    public function test_custom_html_works(){
        $user = \App\Models\User::factory()->create();
        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'custom_html' => (function(){
                return 'The top custom html';
            }),
            'custom_html_bottom' => (function(){
                return 'The bottom custom html';
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);
        $res->assertSee('The top custom html', false);
        $res->assertSee('The bottom custom html', false);
    }

    public function test_search_system_works(){
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);
        $res->assertDontSee('<input style="background-color: #fff !important" value="" name="search" type="text" class="form-control bg-light border-0 small" placeholder="Search here..." aria-label="Search" aria-describedby="basic-addon2">', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'search' => (function($q, $w){
                return $q;
            })
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);
        $res->assertSee('<input style="background-color: #fff !important" value="" name="search" type="text" class="form-control bg-light border-0 small" placeholder="Search here..." aria-label="Search" aria-describedby="basic-addon2">', false);

        $res = $this->actingAs($user)->get('/admin/model/User?search=hello');
        $res->assertStatus(200);
        $res->assertSee('<input style="background-color: #fff !important" value="hello" name="search" type="text" class="form-control bg-light border-0 small" placeholder="Search here..." aria-label="Search" aria-describedby="basic-addon2">', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'search' => (function($q, $w){
                return $q;
            }),
            'search_hint' => 'search for something',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);
        $res->assertSee('<input style="background-color: #fff !important" value="" name="search" type="text" class="form-control bg-light border-0 small" placeholder="search for something" aria-label="Search" aria-describedby="basic-addon2">', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'search' => (function($q, $w){
                // reverse search
                return $q->where('email', '<>', $w);
            }),
            'search_hint' => 'search for something',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);
        $res->assertSee('<td>' . $user->email . '</td>', false);

        $res = $this->actingAs($user)->get('/admin/model/User?search=' . $user->email);
        $res->assertStatus(200);
        $res->assertDontSee('<td>' . $user->email . '</td>', false);
    }
}
