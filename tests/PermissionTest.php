<?php

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
}
