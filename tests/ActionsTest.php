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

class ActionsTest extends TestCase
{
    public function test_action_middleware_works_and_action_will_be_runed()
    {
        $user = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\Post::class, [
            'slug' => 'Post',
            'actions' => [
                'my_btn' => [
                    'title' => 'My Button',
                    'middleware' => (function($u, $row) use ($user){
                        return $u->id === $user->id;
                    }),
                    'run' => (function($row){
                        return $row->body;
                    }),
                ],
            ]
        ]);
        $admin->register('/admin');

        $post = \App\Models\Post::factory()->create();

        $res = $this->actingAs($user)->post('/admin/model/Post', ['action' => 'my_btn111', 'id' => 123]);
        $res->assertStatus(400);

        $res = $this->actingAs($user)->post('/admin/model/Post', ['action' => 'my_btn', 'id' => 123]);
        $res->assertStatus(400);

        $res = $this->actingAs($user2)->post('/admin/model/Post', ['action' => 'my_btn', 'id' => $post->id]);
        $res->assertStatus(403);

        $res = $this->actingAs($user)->post('/admin/model/Post', ['action' => 'my_btn', 'id' => $post->id]);
        $res->assertStatus(200);
        $res->assertSee($post->body, false);
    }
}
