<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupIdLeave extends BaseModel
{
    protected $table = 'group_id_leaves';
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'group_id');
    }
}
