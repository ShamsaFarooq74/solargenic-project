<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SaltecPushData extends Model
{
    protected $table = "saltec_push_response";

    protected $fillable = ['site_id', 'response', 'collect_time', 'status', 'created_at','updated_at'];
}

