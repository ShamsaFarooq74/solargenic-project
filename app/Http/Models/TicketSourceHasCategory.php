<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TicketSourceHasCategory extends Model
{
    protected $table = 'ticket_source_has_category';

    public function ticket_source()

    {

        return $this->belongsTo(TicketSource::class, 'source_id');

    }

    public function ticket_category()

    {

        return $this->belongsTo(TicketCategory::class, 'category_id');

    }
}
