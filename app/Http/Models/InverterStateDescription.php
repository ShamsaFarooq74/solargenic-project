<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class InverterStateDescription extends Model

{

    protected $table = 'inverter_state_description';

    protected $fillable = ['plant_meter_type', 'state_code', 'decimal_code', 'code_description'];
}

