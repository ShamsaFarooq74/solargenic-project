<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TicketSource extends Model
{
    public function ticket_source_has_category()

    {
        return $this->hasMany(TicketSourceHasCategory::class, 'source_id');
    }

}
