<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $table = "ticket_agent";
    protected $fillable = [
        'id','ticket_id','employee_id' ];


    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id','ticket_id');
    }



//    public function ticket()
//    {
//        return $this->hasMany(Ticket::class, 'id','ticket_id');
//    }
//
//    public function agent()
//    {
//        return $this->belongsTo(Agent::class,'id','ticket_agent_id');
//    }

}
