<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class YearlyProcessedPlantEMIDetail extends Model

{

    protected $table = "yearly_processed_plant_emi_detail";

    protected $fillable = ['plant_id','yearly_irradiance','created_at','updated_at'];


}

