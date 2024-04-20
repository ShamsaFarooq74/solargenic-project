<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;


class DailyProcessedPlantEMIDetail extends Model

{

    protected $table = "daily_processed_plant_emi_detail";

    protected $fillable = ['plant_id','daily_irradiance','created_at','updated_at'];

}

