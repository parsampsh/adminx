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

    public function test_n2n_relation_is_handled()
    {
        $user = \App\Models\User::factory()->create();
        $category1 = \App\Models\Category::factory()->create();
        $category2 = \App\Models\Category::factory()->create();

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
            'n2n' => [
                [
                    'name' => 'Categories',
                    'list' => (function(){
                        return \App\Models\Category::all();
                    }),
                    'title' => (function($post){
                        return $post->title;
                    }),
                    'model' => \App\Models\Category::class,
                    'pivot' => \App\Models\PostCategory::class,
                    'pivot_keys' => ['post_id', 'category_id'],
                ],
            ],
        ]);
        $admin->register('/admin');

        $res = $this->actingAs($user)->get('/admin/model/Post/create');
        $res->assertOk();
        $res->assertSee('Categories:', false);

        $res = $this->actingAs($user)->post('/admin/model/Post/create', [
            'body' => 'hello post',
            'user_id' => $user->id,
            'n2n1' => [
                $category1->id,
            ],
        ]);
        $res->assertStatus(302);

        $created_post = \App\Models\Post::where('body', 'hello post')->first();
        $this->assertNotEmpty($created_post);

        $set_categories = $created_post->belongsToMany(\App\Models\Category::class, 'post_categories')->get();
        $this->assertEquals(count($set_categories), 1);
        $this->assertEquals($set_categories[0]->title, $category1->title);

        $res = $this->actingAs($user)->get('/admin/model/Post/update/' . $created_post->id);
        $res->assertOk();
        $res->assertSee('Categories:', false);

        $res = $this->actingAs($user)->put('/admin/model/Post/update/' . $created_post->id, [
            'body' => 'hello post',
            'user_id' => $user->id,
            'n2n1' => [
                $category2->id,
            ],
        ]);
        $res->assertStatus(302);

        $created_post->refresh();

        $set_categories = $created_post->belongsToMany(\App\Models\Category::class, 'post_categories')->get();
        $this->assertEquals(count($set_categories), 1);
        $this->assertEquals($set_categories[0]->title, $category2->title);
    }
}
