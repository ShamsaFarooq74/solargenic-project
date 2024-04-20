<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PlantDetail extends Model
{
    protected $table = "plant_details";
    protected $fillable = ['plant_id','siteId','l1GridCurrent','l2GridCurrent','l3GridCurrent','l1GridApparentPower','l2GridApparentPower','l3GridApparentPower','l1GridPowerFactor','l2GridPowerFactor','l3GridPowerFactor','gridFrequency','totalGridApparentPower','meterUptime','gecUptime','installedMeterType','installedInverterType','meterCommFail','exportLimitEnabled','l1Voltage','l2Voltage','l3Voltage','l1GridPower','l2GridPower','l3GridPower','totalGridPower','l1LoadPower','l2LoadPower','l3LoadPower','totalLoadPower','importEnergy','exportEnergy','consumedEnergy','solarEnergy','l1InverterPower','l2InverterPower','l3InverterPower','totalInverterPower','numberOfInverters','lastUpdated','created_at','updated_at',
    ];

    public function plants()
    {
        return $this->belongsTo(Plant::class);
    }
}
