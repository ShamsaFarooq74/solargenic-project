<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = "messages";
    protected $fillable = [
        'user_id','subject','description','platform','created_at'
    ];


}
