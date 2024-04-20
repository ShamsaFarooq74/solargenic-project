<?php

namespace App\Http\Models;
use Illuminate\Database\Eloquent\Model;

class InverterEMIDetail extends Model
{
    protected $table = 'inverter_emi_details';

    protected $fillable = [
        'plant_id', 'site_id', 'dv_inverter', 'dv_inverter_serial_no', 'temperature', 'pv_temperature', 'wind_speed', 'wind_direction', 'radiant_total', 'radiant_line', 'horiz_radiant_line', 'horiz_radiant_total', 'collect_time', 'created_at', 'updated_at'
    ];
}
