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

class ForeignKeyTest extends TestCase
{
    public function test_foreign_key_is_handled_in_table_and_form()
    {
        $user = \App\Models\User::factory()->create();
        $post = \App\Models\Post::factory()->create(['user_id' => $user->id]);

        $admin = new \Adminx\Core;
        $admin->super_user(function(){
            return true;
        });
        $admin->add_model(\App\Models\Post::class, [
            'slug' => 'Post',
            'foreign_keys' => [
                'user_id' => [
                    'model' => \App\Models\User::class,
                    'list' => (function(){
                        return \App\Models\User::all();
                    }),
                    'title' => (function($row){
                        return $row->email;
                    }),
                ]
            ],
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/Post');
        $res->assertOk();
        $res->assertSee('<td>' . $user->email . '</td>', false);

        $res = $this->actingAs($user)->get('/admin/model/Post/create');
        $res->assertOk();
        $res->assertSee('<option value="' . $user->id . '">' . $user->email . '</option>', false);
    }
}
