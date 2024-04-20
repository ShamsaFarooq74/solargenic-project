<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class DailyInverterDetail extends Model

{

    protected $table = "daily_inverter_detail";

    protected $fillable = [

        'plant_id','siteId','daily_energy_purchased','daily_grid_feed_in','dv_inverter','serial_no','daily_generation','daily_consumption','lastUpdated','daily_charge_energy','daily_discharge_energy','created_at','updated_at'

    ];

    protected $casts = [
        'daily_generation'=>'string',
    ];



    public function plants()

    {

        return $this->belongsTo(Plant::class);

    }



    public function inverters()

    {

        return $this->belongsTo(Inverter::class);

    }

}

