<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedCurrentVariableHistory extends Model
{
    protected $table = 'processed_current_variable_history';

    protected $fillable = [

        'plant_id', 'current_generation', 'current_consumption','battery_charge','battery_discharge', 'current_grid', 'grid_type', 'current_irradiance', 'totalEnergy','current_saving', 'comm_failed', 'processed_cron_job_id','battery_power','battery_type','battery_capacity','total_charge_energy','total_discharge_power', 'created_at', 'collect_time'
    ];
}
