<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class MonthlyProcessedPlantEMIDetail extends Model

{

    protected $table = "monthly_processed_plant_emi_detail";

    protected $fillable = ['plant_id','monthly_irradiance','created_at','updated_at'];


}

