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

class UpdateSystemTest extends TestCase
{
    public function test_update_button_will_be_showed(){
        $user = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertDontSee('class="btn btn-primary">Update</a>', false);

        \Adminx\Access::add_permission_for_user($user, 'the-users.update');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertSee('class="btn btn-primary">Update</a>', false);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'update_middleware' => (function(){
                return false;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users');
        $res->assertStatus(200);
        $res->assertDontSee('class="btn btn-primary">Update</a>', false);
    }

    public function test_user_needs_permission_to_see_update_page()
    {
        $user = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users/update/' . $user2->id);
        $res->assertStatus(403);

        \Adminx\Access::add_permission_for_user($user, 'the-users.update');

        $res = $this->actingAs($user)->get('/admin/model/the-users/update/' . $user2->id);
        $res->assertStatus(200);

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'the-users',
            'update_middleware' => (function(){
                return false;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/the-users/update/' . $user2->id);
        $res->assertStatus(403);
    }

    public function test_update_form_is_valid()
    {
        $user = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();

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

        $res = $this->actingAs($user)->get('/admin/model/User/update/' . $user2->id);
        $res->assertStatus(200);
        $res->assertSee('value="' . $user2->email . '"', false);

        $admin = new \Adminx\Core;
        $admin->super_user(function($u) use ($user){
            return $user->id === $u->id;
        });
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
            'readonly_fields' => ['email'],
            'only_editable_fields' => ['email'],
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/User/update/' . $user2->id);
        $res->assertStatus(200);
        $res->assertSee('email:', false);
        $res->assertSee('name="email" class="form-control" />', false);
        $res->assertSee('value="' . $user2->email . '"', false);
    }

    public function test_update_form_should_be_validatad_and_item_can_be_updated()
    {
        $user = \App\Models\User::factory()->create();
        $post = \App\Models\Post::factory()->create();
        $old_body = $post->body;

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
            'filter_update_data' => (function($old, $post){
                $post->body .= '-filter-' . $old->body;
                return $post;
            }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->put('/admin/model/Post/update/' . $post->id, [

        ]);
        $res->assertStatus(302);

        $res = $this->actingAs($user)->put('/admin/model/Post/update/' . $post->id, [
            'body' => 'hello world',
            'user_id' => 1000,
        ]);
        $res->assertStatus(400);

        $res = $this->actingAs($user)->put('/admin/model/Post/update/' . $post->id, [
            'body' => 'hello world',
            'user_id' => $user->id,
        ]);
        $res->assertStatus(302);

        $post->refresh();
        $this->assertEquals('hello world-filter-' . $old_body, $post->body);
    }

    public function test_custom_html_is_showed()
    {
        $user = \App\Models\User::factory()->create();
        $post = \App\Models\Post::factory()->create();

        $admin = new \Adminx\Core;
        $admin->super_user(function($u) use ($user){
            return $user->id === $u->id;
        });
        $admin->add_model(\App\Models\Post::class, [
            'slug' => 'Post',
            'update_html' => (function($row){ return 'hello ' . $row->id; }),
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/Post/update/' . $post->id);
        $res->assertStatus(200);
        $res->assertSee('hello ' . $post->id, false);
    }
}
