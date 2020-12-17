<?php

namespace Adminx\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'adminx_groups';

    public function permissions(){
        return $this->hasMany(GruopPermission::class);
    }
}
