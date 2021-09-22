<?php

namespace App;

use App\Attendance;
use Illuminate\Database\Eloquent\Model;
use App\Observers\StandupObserver;
use App\Scopes\CompanyScope;

class Standup extends BaseModel
{
    protected $table = 'standups';

    protected static function boot()
    {
        parent::boot();

        static::observe(StandupObserver::class);

        static::addGlobalScope(new CompanyScope);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }
}
