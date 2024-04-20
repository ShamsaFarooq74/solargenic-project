<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class YearlyProcessedPlantDetail extends Model

{

    protected $table = "yearly_processed_plant_detail";

    protected $fillable =
    ['plant_id','siteId','yearly_outage_grid_voltage','yearlyGeneration','yearly_peak_hours_discharge_energy','yearly_peak_hours_grid_import','yearly_peak_hours_consumption','yearlyConsumption','yearlyGridPower','yearlyBoughtEnergy','yearlySellEnergy','yearlyMaxSolarPower','yearly_charge_energy','yearly_discharge_energy','yearlySaving','yearlyIrradiance','yearlyGridLoad','yearlyBackupLoad','lastUpdated','created_at','updated_at'];



    public function plants()

    {

        return $this->belongsTo(Plant::class);

    }

}

