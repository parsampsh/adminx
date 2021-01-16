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

class CreateSystemTest extends TestCase
{
    public function test_create_button_will_be_showed(){
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertDontSee('<a class="btn btn-success" href="'. $admin->url('/model/the-users/create?back=' . request()->fullUrl()) . '">'. str_replace('{name}', 'the-users', $admin->get_word('btn.create', 'Create new {name}')) .' <i class="fa fa-plus"></i></a>', false);

        \Adminx\Access::add_permission_for_user($user, 'the-users.create');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertSee('<a class="btn btn-success" href="'. $admin->url('/model/the-users/create?back=' . request()->fullUrl()) . '">'. str_replace('{name}', 'the-users', $admin->get_word('btn.create', 'Create new {name}')) .' <i class="fa fa-plus"></i></a>', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'create_middleware' => (function(){
                return false;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertDontSee('<a class="btn btn-success" href="'. $admin->url('/model/the-users/create?back=' . request()->fullUrl()) . '">'. str_replace('{name}', 'the-users', $admin->get_word('btn.create', 'Create new {name}')) .' <i class="fa fa-plus"></i></a>', false);
    }

    public function test_user_needs_permission_to_see_create_page()
    {
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users/create');
        $res->assertStatus(403);

        \Adminx\Access::add_permission_for_user($user, 'the-users.create');

        $res = $this->actingAs($user)->get('/admin/model/the-users/create');
        $res->assertStatus(200);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'create_middleware' => (function(){
                return false;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users/create');
        $res->assertStatus(403);
    }
}
