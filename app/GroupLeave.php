<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupLeave extends BaseModel
{
    protected $table = 'group_leaves';
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'group_leave_id');
    }
}
