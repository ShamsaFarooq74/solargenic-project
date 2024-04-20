<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class ProcessedPlantDetail extends Model

{

    protected $table = "processed_plant_detail";

    protected $fillable = ['plant_id','siteId','dailyGeneration','monthlyGeneration','yearlyGeneration','dailyConsumption','monthlyConsumption','yearlyConsumption','dailyGridPower','monthlyGridPower','yearlyGridPower','dailyBoughtEnergy','monthlyBoughtEnergy','yearlyBoughtEnergy','dailySellEnergy','monthlySellEnergy','yearlySellEnergy','dailyMaxSolarPower','monthlyMaxSolarPower','yearlyMaxSolarPower','cron_job_id','cron_job_type','lastUpdated','created_at','updated_at'];



    public function plants()

    {

        return $this->belongsTo(Plant::class);

    }

}
