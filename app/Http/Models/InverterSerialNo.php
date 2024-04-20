<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class InverterSerialNo extends Model
{
    protected $table = 'inverter_serial_no';

    protected $fillable = [
        'plant_id', 'site_id', 'dv_inverter', 'dv_inverter_serial_no', 'inverter_type_id', 'inverter_name','status', 'created_at', 'updated_at'
    ];

    public function inverter_mppt_detail()
    {
        return $this->hasMany(InverterMPPTDetail::class, 'dv_inverter', 'dv_inverter');
    }

    public function latest_inverter_mppt_detail()
    {
        return $this->inverter_mppt_detail()->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'DESC');
    }
}
