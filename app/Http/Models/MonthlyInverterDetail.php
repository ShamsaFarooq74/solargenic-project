<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class MonthlyInverterDetail extends Model

{

    protected $table = "monthly_inverter_detail";

    protected $fillable = [

        'plant_id','siteId','monthly_energy_purchased','monthly_grid_feed_in','monthly_consumption_energy','dv_inverter','serial_no','monthly_generation','lastUpdated','monthly_charge_energy','monthly_discharge_energy','created_at','updated_at'

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

