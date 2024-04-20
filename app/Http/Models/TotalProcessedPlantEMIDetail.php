<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;


class TotalProcessedPlantEMIDetail extends Model

{

    protected $table = "total_processed_plant_emi_detail";

    protected $fillable = ['plant_id','total_irradiance','created_at','updated_at'];

}

