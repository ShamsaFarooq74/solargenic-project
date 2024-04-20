<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    protected $table = "ticket_status";

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
    
}
