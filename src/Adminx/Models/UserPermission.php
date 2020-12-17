<?php

namespace Adminx\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $table = 'adminx_permissions';

    public function user(){
        return $this->belongsTo(\App\Models\User::class);
    }
}
