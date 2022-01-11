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

class SuperUserTest extends TestCase
{
    public function test_super_user_can_do_anything()
    {
        $user = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();

        $admin = new \Adminx\Core;
        $admin->add_model(\App\Models\User::class, [
            'slug' => 'User',
        ]);
        $admin->super_user(function($user){
            return true;
        });
        $admin->register('/admin');

        $this->actingAs($user)->get('/admin/model/User')->assertOk();
        $this->actingAs($user)->get('/admin/model/User/create')->assertOk();
        $this->actingAs($user)->delete('/admin/model/User', ['delete' => $user2->id])->assertStatus(302);
        $this->assertEmpty(\App\Models\User::find($user2->id));
    }
}
