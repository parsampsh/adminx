<?php

namespace Adminx\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Permissions of a user
 *
 * Also alongside groups, users can have permissions
 */
class UserPermission extends Model
{
    protected $table = 'adminx_user_permissions';

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
