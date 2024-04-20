<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = "tickets";
    protected $fillable = [
        'id','plant_id','company_id','status','priority','source','category','sub_category','due_in','alternate_email','alternate_contact','title','notify_by','received_medium','user_approved','platform','created_by','updated_by','created_at','updated_at'
    ];

    public function attachment()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function history()
    {
        return $this->hasMany(TicketHistory::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function status_check()
    {
        return $this->belongsTo(TicketStatus::class,'status','id');
    }

    public function priority_check()
    {
        return $this->belongsTo(TicketPriority::class,'priority','id');
    }

    public function source_check()
    {
        return $this->belongsTo(TicketSource::class,'source','id');
    }

    public function due_in_check()
    {
        return $this->belongsTo(TicketDueIn::class,'due_in','id');
    }


    public function ticket_agent()
    {
        return $this->hasMany(TicketAgent::class,'ticket_id','id');
    }

}
