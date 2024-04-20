<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class GenerationLogMicrotech extends Model
{
    protected $table = "microtech_energy_generation_log";

    protected $fillable =
    ['meter_serial_no','active_energy_pos_tl','active_energy_neg_tl','meter_datetime','mdc_read_datetime','db_datetime','created_at','updated_at'];
}
