<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PlantType extends Model
{
    protected $table = 'plant_type';

    public function plant()

    {

        return $this->belongsTo(Plant::class);

    }

}
