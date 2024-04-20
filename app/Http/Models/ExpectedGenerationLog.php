<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class ExpectedGenerationLog extends Model

{

    protected $table = 'expected_generation';

    protected $fillable = [

        'plant_id','daily_expected_generation','created_at','updated_at'

    ];



    public function plants()

    {

        return $this->belongsTo(Plant::class);

    }



}

