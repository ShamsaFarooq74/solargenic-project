<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class Inverter extends Model

{

    protected $fillable = [

        'plant_id','siteId','dv_inverter','serial_no','ac_output_power','total_generation','l_voltage1','l_current1','l_voltage2','l_current2','l_voltage3','l_current3','r_voltage1','r_current1','r_voltage2','r_current2','r_voltage3','r_current3','frequency','dc_power','lastUpdated','created_at','updated_at'

    ];

protected $casts = [
'plant_id'=>'string',
'ac_output_power'=>'string',
'total_generation'=>'string',
'l_voltage1'=>'string',
'l_current1'=>'string',
'l_voltage2'=>'string',
'l_current2'=>'string',
'l_voltage3'=>'string',
'l_current3'=>'string',
'r_voltage1'=>'string',
'r_current1'=>'string',
'r_voltage2'=>'string',
'r_current2'=>'string',
'r_voltage3'=>'string',
'r_current3'=>'string',
'frequency'=>'string',
'dc_power'=>'string'
];

    public function plants()

    {

        return $this->belongsTo(Plant::class);

    }



    public function daily_inverter_detail()

    {

        return $this->hasMany(DailyInverterDetail::class,'serial_no');

    }



	public function monthly_inverter_detail()

    {

        return $this->hasMany(MonthlyInverterDetail::class,'serial_no');

    }

}

