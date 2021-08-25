<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\CompanyScope;
use App\Traits\CustomFieldsTrait;

class BankAccount extends BaseModel
{
    use CustomFieldsTrait;

    protected $table = 'bank_accounts';
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }
}
