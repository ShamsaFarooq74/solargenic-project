<?php

namespace App\Http\Models;
use Illuminate\Database\Eloquent\Model;

class InverterVersionInformation extends Model
{
    protected $table = 'inverter_version_information';
    protected $fillable = ['plant_id', 'site_id', 'dv_inverter', 'general_settings', 'production_compliance', 'rated_power','protocol_version', 'control_software_version', 'communication_cpu_software', 'HMI', 'lithium_battery_version', 'main_1', 'main_2'];
}
