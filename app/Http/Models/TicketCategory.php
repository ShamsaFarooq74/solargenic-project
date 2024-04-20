<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    protected $table = "ticket_category";

    public function ticket_source_has_category()
    {
        return $this->hasMany(TicketSourceHasCategory::class, 'category_id');
    }
}
