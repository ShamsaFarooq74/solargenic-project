<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class MonthlyProcessedPlantDetail extends Model

{

    protected $table = "monthly_processed_plant_detail";

    protected $fillable =
    ['plant_id','monthly_outage_grid_voltage','monthlyGeneration','monthlyConsumption','monthly_peak_hours_discharge_energy','monthly_peak_hours_grid_import','monthly_peak_hours_consumption','monthlyGridPower','monthlyBoughtEnergy','monthlySellEnergy','monthlyMaxSolarPower','monthly_charge_energy','monthly_discharge_energy','monthlySaving','monthlyIrradiance','monthlyGridLoad','monthlyBackupLoad','lastUpdated','created_at','updated_at'];



    public function plants()

    {

        return $this->belongsTo(Plant::class);

    }

}

