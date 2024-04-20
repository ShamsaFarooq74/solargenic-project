<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class UserCompany extends Model
{
    protected $table = 'user_companies';

    public function company()

    {

        return $this->belongsTo(Company::class, 'company_id');

    }
}
