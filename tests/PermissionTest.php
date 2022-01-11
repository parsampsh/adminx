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
use \Adminx\Access;

class PermissionTest extends TestCase
{
    public function test_user_has_permission_works()
    {
        $user = \App\Models\User::factory()->create();

        $per1 = new \Adminx\Models\UserPermission;
        $per1->permission = 'per1';
        $per1->flag = true;
        $per1->user_id = $user->id;
        $per1->save();

        $this->assertTrue(Access::user_has_permission($user, 'per1'));
        $this->assertFalse(Access::user_has_permission($user, 'per1111'));

        $per1->flag = false;
        $per1->save();

        $this->assertFalse(Access::user_has_permission($user, 'per1'));

        $per1->delete();

        $group1 = new \Adminx\Models\Group;
        $group1->name = 'test-group';
        $group1->save();

        $per1 = new \Adminx\Models\GroupPermission;
        $per1->permission = 'gper1';
        $per1->flag = true;
        $per1->adminx_group_id = $group1->id;
        $per1->flag = true;
        $per1->save();

        $this->assertFalse(Access::user_has_permission($user, 'gper1'));

        $ug = new \Adminx\Models\UserGroup;
        $ug->user_id = $user->id;
        $ug->adminx_group_id = $group1->id;
        $ug->save();

        $this->assertTrue(Access::user_has_permission($user, 'gper1'));

        $per2 = new \Adminx\Models\UserPermission;
        $per2->permission = 'gper1';
        $per2->flag = false;
        $per2->user_id = $user->id;
        $per2->save();

        $this->assertFalse(Access::user_has_permission($user, 'gper1'));
    }

    public function test_add_permission_for_user_works()
    {
        $user = \App\Models\User::factory()->create();

        $this->assertFalse(Access::user_has_permission($user, 'test-per'));

        Access::add_permission_for_user($user, 'test-per');

        $this->assertTrue(Access::user_has_permission($user, 'test-per'));

        Access::add_permission_for_user($user, 'test-per', false);

        $this->assertFalse(Access::user_has_permission($user, 'test-per'));
    }

    public function test_add_user_to_group_works()
    {
        $user = \App\Models\User::factory()->create();

        $group1 = new \Adminx\Models\Group;
        $group1->name = 'test-group';
        $group1->save();

        $this->assertFalse(Access::user_is_in_group($user, $group1));

        Access::add_user_to_group($user, $group1);

        $this->assertTrue(Access::user_is_in_group($user, $group1));

        Access::remove_user_from_group($user, $group1);

        $this->assertFalse(Access::user_is_in_group($user, $group1));
    }

    public function test_add_permission_for_group_works()
    {
        $user = \App\Models\User::factory()->create();

        $group1 = new \Adminx\Models\Group;
        $group1->name = 'test-group';
        $group1->save();

        Access::add_user_to_group($user, $group1);

        $this->assertTrue(Access::user_is_in_group($user, $group1));

        $this->assertFalse(Access::user_has_permission($user, 'test-per'));

        Access::add_permission_for_group($group1, 'test-per');

        $this->assertTrue(Access::user_has_permission($user, 'test-per'));

        Access::add_permission_for_group($group1, 'test-per', false);

        $this->assertFalse(Access::user_has_permission($user, 'test-per'));

        Access::add_permission_for_group($group1, 'test-per');

        $this->assertTrue(Access::user_has_permission($user, 'test-per'));

        Access::remove_permission_from_group($group1, 'test-per');

        $this->assertFalse(Access::user_has_permission($user, 'test-per'));
    }
}
