<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    protected $table = "ticket_history";
    protected $fillable = [
        'id','ticket_id','user_id','status','description','is_default','history_changes','created_at','updated_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
