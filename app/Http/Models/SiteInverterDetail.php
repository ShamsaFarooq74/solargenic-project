<?php

namespace App\Http\Models;
use Illuminate\Database\Eloquent\Model;

class SiteInverterDetail extends Model
{
    protected $table = 'site_inverter_details';

    protected $fillable = [
        'plant_id', 'site_id', 'dv_inverter', 'dv_inverter_serial_no', 'dv_inverter_name', 'dv_installed_dc_power', 'dv_inverter_type', 'longitude', 'latitude','created_at', 'updated_at'
    ];
}
