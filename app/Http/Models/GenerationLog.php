<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class GenerationLog extends Model

{

    protected $table = "generation_log";

    protected $fillable = ['plant_id','siteId','current_generation','current_consumption','current_grid','current_irradiance','totalEnergy','comm_failed','cron_job_id','battery_power','battery_type','battery_capacity','lastUpdated','collect_time','created_at','updated_at'];



    public function plant()

    {

        return $this->belongsTo(Plant::class);

    }

}
