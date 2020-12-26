<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under GPL-v3.
 * For more information, please see the LICENSE file.
 */

namespace Adminx\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * The adminx Group
 *
 * Groups has some permissions and that users are in A Group
 * have all of permissions in that groups
 */
class Group extends Model
{
    protected $table = 'adminx_groups';

    public function permissions()
    {
        return $this->hasMany(GroupPermission::class, 'adminx_group_id');
    }
}
