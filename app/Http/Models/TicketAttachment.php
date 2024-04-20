<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $table = "ticket_attachment";
    protected $fillable = [
        'ticket_description_id','attachment_type','attachment','created_at','updated_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
