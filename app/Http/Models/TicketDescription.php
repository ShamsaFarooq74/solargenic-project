<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TicketDescription extends Model
{
    protected $table = 'ticket_description';

    protected $fillable = [
        'user_id','ticket_id','status_id','description','is_default'
    ];
}
