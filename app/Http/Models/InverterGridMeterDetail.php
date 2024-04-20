<?php

namespace App\Http\Models;
use Illuminate\Database\Eloquent\Model;

class InverterGridMeterDetail extends Model
{
    protected $table = 'inverter_grid_meter_details';

    protected $fillable = [
        'plant_id', 'site_id', 'dv_inverter', 'grid_power', 'collect_time', 'created_at', 'updated_at'
    ];
}
