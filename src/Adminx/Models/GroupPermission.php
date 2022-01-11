<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2022 Parsa Shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

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
