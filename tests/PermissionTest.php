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
    public function test_userHasPermission_works()
    {
        $user = \App\Models\User::factory()->create();

        $per1 = new \Adminx\Models\UserPermission;
        $per1->permission = 'per1';
        $per1->flag = true;
        $per1->user_id = $user->id;
        $per1->save();

        $this->assertTrue(Access::userHasPermission($user, 'per1'));
        $this->assertFalse(Access::userHasPermission($user, 'per1111'));

        $per1->flag = false;
        $per1->save();

        $this->assertFalse(Access::userHasPermission($user, 'per1'));

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

        $this->assertFalse(Access::userHasPermission($user, 'gper1'));

        $ug = new \Adminx\Models\UserGroup;
        $ug->user_id = $user->id;
        $ug->adminx_group_id = $group1->id;
        $ug->save();

        $this->assertTrue(Access::userHasPermission($user, 'gper1'));

        $per2 = new \Adminx\Models\UserPermission;
        $per2->permission = 'gper1';
        $per2->flag = false;
        $per2->user_id = $user->id;
        $per2->save();

        $this->assertFalse(Access::userHasPermission($user, 'gper1'));
    }

    public function test_addPermissionForUser_works()
    {
        $user = \App\Models\User::factory()->create();

        $this->assertFalse(Access::userHasPermission($user, 'test-per'));

        Access::addPermissionForUser($user, 'test-per');

        $this->assertTrue(Access::userHasPermission($user, 'test-per'));

        Access::addPermissionForUser($user, 'test-per', false);

        $this->assertFalse(Access::userHasPermission($user, 'test-per'));
    }

    public function test_addUserToGroup_works()
    {
        $user = \App\Models\User::factory()->create();

        $group1 = new \Adminx\Models\Group;
        $group1->name = 'test-group';
        $group1->save();

        $this->assertFalse(Access::userIsInGroup($user, $group1));

        Access::addUserToGroup($user, $group1);

        $this->assertTrue(Access::userIsInGroup($user, $group1));

        Access::removeUserFromGroup($user, $group1);

        $this->assertFalse(Access::userIsInGroup($user, $group1));
    }

    public function test_addPermissionForGroup_works()
    {
        $user = \App\Models\User::factory()->create();

        $group1 = new \Adminx\Models\Group;
        $group1->name = 'test-group';
        $group1->save();

        Access::addUserToGroup($user, $group1);

        $this->assertTrue(Access::userIsInGroup($user, $group1));

        $this->assertFalse(Access::userHasPermission($user, 'test-per'));

        Access::addPermissionForGroup($group1, 'test-per');

        $this->assertTrue(Access::userHasPermission($user, 'test-per'));

        Access::addPermissionForGroup($group1, 'test-per', false);

        $this->assertFalse(Access::userHasPermission($user, 'test-per'));

        Access::addPermissionForGroup($group1, 'test-per');

        $this->assertTrue(Access::userHasPermission($user, 'test-per'));

        Access::removePermissionFromGroup($group1, 'test-per');

        $this->assertFalse(Access::userHasPermission($user, 'test-per'));
    }
}
