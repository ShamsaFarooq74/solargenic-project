<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class YearlyInverterDetail extends Model

{

    protected $table = "yearly_inverter_detail";

    protected $fillable = [

        'plant_id','siteId','dv_inverter','serial_no','yearly_generation','yearly_charge_energy','yearly_discharge_energy','lastUpdated','created_at','updated_at'

    ];



    public function plants()

    {

        return $this->belongsTo(Plant::class);

    }

}

