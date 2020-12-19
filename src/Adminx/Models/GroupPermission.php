<?php

namespace Adminx\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Permissions of a Group
 */
class GroupPermission extends Model
{
    protected $table = 'adminx_group_permissions';

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
