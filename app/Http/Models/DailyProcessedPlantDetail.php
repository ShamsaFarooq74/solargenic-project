<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class DailyProcessedPlantDetail extends Model

{

    protected $table = "daily_processed_plant_detail";

    protected $fillable =['plant_id','daily_outage_grid_voltage','dailyGeneration','soc','daily_peak_hours_consumption','daily_peak_hours_grid_buy','daily_peak_hours_battery_discharge','battery_power','dailyConsumption','dailyGridPower','dailyBoughtEnergy','dailySellEnergy','dailyMaxSolarPower','daily_charge_energy','daily_discharge_energy','dailySaving','dailyIrradiance','grid_ratio','charge_ratio','generation_value','generation_ratio','use_value','use_ratio','grid_value','dailyGridLoad','dailyBackupLoad','created_at','updated_at'];



    public function plants()

    {

        return $this->belongsTo(Plant::class);

    }

}

