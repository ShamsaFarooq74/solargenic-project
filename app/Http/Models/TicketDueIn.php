<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TicketDueIn extends Model
{
    protected $table = "tickets_due_in";

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

}
