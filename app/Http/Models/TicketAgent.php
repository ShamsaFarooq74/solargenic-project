<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAgent extends Model
{
    protected $table = "ticket_agent";
    protected $fillable = [
        'id','ticket_id','employee_id' ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id','ticket_id');
    }
}
