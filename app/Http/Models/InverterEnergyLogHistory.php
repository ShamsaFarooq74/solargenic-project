<?php

namespace App\Http\Models;
use Illuminate\Database\Eloquent\Model;

class InverterEnergyLogHistory extends Model
{
    protected $table = 'inverter_energy_log_history';

    protected $fillable = [
        'plant_id', 'site_id', 'dv_inverter', 'grid_power', 'import_energy', 'export_energy', 'collect_time', 'cron_job_id', 'created_at', 'updated_at','grid_type','total_grid_feed_in','grid_voltage_r_u_a','grid_current_r_u_a','phase_grid_power','total_grid_voltage','grid_frequency','total_grid_power','meter_total_active_power','total_energy_purchased','meter_active_power','meter_ac_current'];
}
