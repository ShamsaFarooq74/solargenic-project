<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PlantMPPT extends Model
{
    protected $table = 'plant_mppt';

    protected $fillable = [
        'plant_id', 'total_mppt', 'string', 'string_mppt', 'created_at', 'updated_at'
    ];
}
