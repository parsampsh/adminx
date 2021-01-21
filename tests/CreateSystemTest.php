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

class CreateSystemTest extends TestCase
{
    public function test_create_button_will_be_showed(){
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);
        $res->assertDontSee('<a class="btn btn-success" href="'. $admin->url('/model/User/create?back=' . request()->fullUrl()) . '">'. str_replace('{name}', 'the-users', $admin->get_word('btn.create', 'Create new {name}')) .' <i class="fa fa-plus"></i></a>', false);

        \Adminx\Access::add_permission_for_user($user, 'User.create');

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);
        $res->assertSee('<a class="btn btn-success" href="'. $admin->url('/model/User/create?back=' . request()->fullUrl()) . '">'. str_replace('{name}', 'User', $admin->get_word('btn.create', 'Create new {name}')) .' <i class="fa fa-plus"></i></a>', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'create_middleware' => (function(){
                return false;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User');
        $res->assertStatus(200);
        $res->assertDontSee('<a class="btn btn-success" href="'. $admin->url('/model/User/create?back=' . request()->fullUrl()) . '">'. str_replace('{name}', 'the-users', $admin->get_word('btn.create', 'Create new {name}')) .' <i class="fa fa-plus"></i></a>', false);
    }

    public function test_user_needs_permission_to_see_create_page()
    {
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User/create');
        $res->assertStatus(403);

        \Adminx\Access::add_permission_for_user($user, 'User.create');

        $res = $this->actingAs($user)->get('/admin/model/User/create');
        $res->assertStatus(200);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'create_middleware' => (function(){
                return false;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User/create');
        $res->assertStatus(403);
    }

    public function test_create_form_is_valid()
    {
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->super_user(function($u) use ($user){
            return $user->id === $u->id;
        });
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'fields_titles' => [
                'email' => 'User Email',
            ],
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User/create');
        $res->assertStatus(200);
        $res->assertDontSee('name="id" class="form-control" />', false);
        $res->assertSee('User Email:', false);
        $res->assertSee('name="email" class="form-control" />', false);

        $admin = new \Adminx\Core;
        $admin->super_user(function($u) use ($user){
            return $user->id === $u->id;
        });
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'readonly_fields' => ['email'],
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User/create');
        $res->assertStatus(200);
        $res->assertDontSee('User Email:', false);
        $res->assertDontSee('name="email" class="form-control" />', false);

        $admin = new \Adminx\Core;
        $admin->super_user(function($u) use ($user){
            return $user->id === $u->id;
        });
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'readonly_fields' => ['email'],
            'only_addable_fields' => ['email'],
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User/create');
        $res->assertStatus(200);
        $res->assertSee('email:', false);
        $res->assertSee('name="email" class="form-control" />', false);
    }

    public function test_create_form_should_be_validatad_and_item_can_be_created()
    {
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->super_user(function($u) use ($user){
            return $user->id === $u->id;
        });
        $admin->add_model(\App\Models\Post::class, [
            'slug' => 'Post',
            'foreign_keys' => [
                'user_id' => [
                    'model' => \App\Models\User::class,
                    'list' => (function(){
                        return \App\Models\User::all();
                    }),
                    'title' => (function($post){
                        return $post->id;
                    }),
                ]
            ],
            'filter_create_data' => (function($post){
                $post->body .= '-filter';
                return $post;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->post('/admin/model/Post/create', [

        ]);
        $res->assertStatus(302);

        $res = $this->actingAs($user)->post('/admin/model/Post/create', [
            'body' => 'hello world',
            'user_id' => 1000,
        ]);
        $res->assertStatus(400);

        $res = $this->actingAs($user)->post('/admin/model/Post/create', [
            'body' => 'hello world',
            'user_id' => $user->id,
        ]);
        $res->assertStatus(302);

        $post = \App\Models\Post::where('body', 'hello world-filter')->where('user_id', $user->id)->first();
        $this->assertNotEmpty($post);
    }

    public function test_custom_html_is_showed()
    {
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->super_user(function($u) use ($user){
            return $user->id === $u->id;
        });
        $admin->add_model(\App\Models\Post::class, [
            'slug' => 'Post',
            'create_html' => (function(){ return 'hello create_html'; }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/Post/create');
        $res->assertStatus(200);
        $res->assertSee('hello create_html', false);
    }
}
