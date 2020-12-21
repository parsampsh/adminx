<?php

/*
 * This file is part of Adminx.
 *   Copyright 2020 parsa shahmaleki <parsampsh@gmail.com>
 * Licensed Under GPL-v3
 * For more information, please view the LICENSE file
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
