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

class DeleteSystemTest extends TestCase
{
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
