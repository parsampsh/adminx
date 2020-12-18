<?php

namespace Adminx;

use Adminx\Models\UserPermission;
use Adminx\Models\Group;

/**
 * The adminx permission and group API
 */
class Access
{
    /**
     * Checks a user has the permission
     */
    public static function user_has_permission($user, $permission): bool
    {
        $groups = $user->belongsToMany(Group::class, 'adminx_user_groups', 'user_id', 'adminx_group_id')->get();
        $allowed_permissions = [];
        $disallowed_permissions = [];
        foreach($groups as $group)
        {
            foreach($group->permissions as $gp)
            {
                if($gp->flag){
                    array_push($allowed_permissions, $gp->permission);
                }else{
                    array_push($disallowed_permissions, $gp->permission);
                }
            }
        }

        foreach(UserPermission::where('user_id', $user->id)->get() as $up)
        {
            if($up->flag){
                array_push($allowed_permissions, $up->permission);
            }else{
                array_push($disallowed_permissions, $up->permission);
            }
        }

        // check user has the permission
        if(in_array($permission, $disallowed_permissions))
        {
            return false;
        }

        if(in_array($permission, $allowed_permissions))
        {
            return true;
        }

        return false;
    }

    /**
     * Adds a permission for user
     * 
     * The $flag is a boolean. if this is true, means user has this permission
     * and if this is false, means user Has NOT this permission
     */
    public static function add_permission_for_user($user, $permission, $flag=true)
    {
        // check already exists
        $up = UserPermission::where('user_id', $user->id)->where('permission', $permission)->first();
        if($up === null){
            $up = new UserPermission;
            $up->user_id = $user->id;
            $up->permission = (string) $permission;
        }
        $up->flag = (bool) $flag;
        return $up->save();
    }
}
