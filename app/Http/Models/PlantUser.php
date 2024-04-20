<?php



namespace App\Http\Models;



use Illuminate\Database\Eloquent\Model;



class PlantUser extends Model

{

    protected $table = 'plant_user';

    protected $fillable = ['user_id','plant_id','is_active'];



    public function user()

    {

        return $this->belongsTo(User::class);

    }

    public function plant()

    {

        return $this->belongsTo(Plant::class, 'plant_id');

    }

}

