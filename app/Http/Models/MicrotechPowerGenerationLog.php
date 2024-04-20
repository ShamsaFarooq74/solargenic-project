<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class MicrotechPowerGenerationLog extends Model
{
    protected $table = "microtech_power_generation_log";

    protected $fillable =
    ['meter_serial_no','current_tariff_register','signal_strength','frequency','meter_datetime','current_phase_a','current_phase_b','current_phase_c','voltage_phase_a','voltage_phase_b','voltage_phase_c','aggregate_active_pwr_pos','aggregate_active_pwr_neg','aggregate_reactive_pwr_pos','aggregate_reactive_pwr_neg','average_pf','mdc_read_datetime','db_datetime','created_at','updated_at'];
}
