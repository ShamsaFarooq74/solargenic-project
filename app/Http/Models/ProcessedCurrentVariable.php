<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedCurrentVariable extends Model
{
    protected $table = 'processed_current_variables';

    protected $fillable = [

        'plant_id', 'current_generation', 'total_backup_Load' ,'current_consumption','battery_charge','battery_discharge', 'current_grid', 'grid_type', 'current_irradiance', 'totalEnergy','current_saving', 'comm_failed', 'processed_cron_job_id' , 'processed_cron_job_type','battery_power','battery_type','battery_capacity','total_charge_energy','total_discharge_power','grid_Load', 'created_at', 'collect_time'
    ];
}
