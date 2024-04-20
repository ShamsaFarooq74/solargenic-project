<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPriority extends Model
{
    protected $table = "ticket_priority";

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
}
