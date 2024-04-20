<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TotalProcessedPlantDetail extends Model
{
    protected $table = "total_processed_plant_detail";

    protected $fillable = [
        'id','plant_id','plant_total_current_power','plant_total_generation','plant_total_consumption','plant_total_grid','plant_total_buy_energy','plant_total_sell_energy','plant_total_charge_energy','plant_total_discharge_energy','plant_total_saving','plant_total_reduction','plant_total_irradiance','plant_total_grid_load','plant_total_backup_load','created_at','updated_at'
    ];
}
