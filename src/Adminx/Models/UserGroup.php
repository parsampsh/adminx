<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2021 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Users of a group
 */
class UserGroup extends Model
{
    protected $table = 'adminx_user_groups';

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }
}
