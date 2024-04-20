<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class InverterMPPTDetail extends Model
{
    protected $table = 'inverter_mppt_detail';

    protected $fillable = [
        'plant_id', 'site_id', 'dv_inverter', 'mppt_number', 'mppt_voltage', 'mppt_current', 'mppt_power', 'collect_time', 'created_at', 'updated_at'
    ];
}
