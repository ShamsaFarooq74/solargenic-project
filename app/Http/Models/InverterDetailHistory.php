<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class InverterDetailHistory extends Model
{
    protected $table = "inverter_detail_history";

    protected $fillable = ['plant_id','siteId','dv_inverter','serial_no','current_consumption','daily_consumption','daily_generation','monthly_generation','inverterPower','totalInverterPower','inverterEnergy','inverterLimitValue','inverterCommFail','inverterConfigFail','inverterUptime','numberOfInverters','inverterEfficieny','inverterTemperature','inverterState','inverterStateCode','mpptPower', 'frequency', 'start_time', 'phase_voltage_r', 'phase_voltage_s', 'phase_voltage_t', 'phase_current_r', 'phase_current_s', 'phase_current_t', 'inverter_cron_job_id', 'lastUpdated', 'collect_time', 'created_at','updated_at'];
}

